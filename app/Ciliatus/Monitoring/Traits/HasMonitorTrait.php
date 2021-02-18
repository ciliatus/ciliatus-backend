<?php

namespace App\Ciliatus\Monitoring\Traits;

use App\Ciliatus\Common\Enum\DatabaseDataTypesEnum;
use App\Ciliatus\Common\Enum\PropertyTypesEnum;
use App\Ciliatus\Common\Models\Model;
use App\Ciliatus\Common\Models\Property;
use App\Ciliatus\Common\Models\Setting;
use App\Ciliatus\Monitoring\Models\LogicalSensor;
use App\Ciliatus\Monitoring\Models\LogicalSensorType;
use App\Ciliatus\Monitoring\Models\PhysicalSensor;
use Carbon\Carbon;
use DemeterChain\C;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HasMonitorTrait
{

    /**
     * @var array
     */
    public ?array $_monitor;

    /**
     *
     */
    public function refreshMonitor(): Model
    {
        if (!method_exists($this, 'physical_sensors')) {
            Log::warning(sprintf('refreshMonitor() called on object not using HasSensorsTrait: %s', __CLASS__));
            return $this;
        }

        /*
         * Collect logical sensor readings
         */
        $monitor_values = [];
        $this->physical_sensors->each(function (PhysicalSensor $physical_sensor) use (&$monitor_values) {
            $physical_sensor->logical_sensors->each(function (LogicalSensor $logical_sensor) use (&$monitor_values) {
                if (!isset($monitor_values[$logical_sensor->logical_sensor_type->id])) {
                    $monitor_values[$logical_sensor->logical_sensor_type->id] = [];
                }

                $monitor_values[$logical_sensor->logical_sensor_type->id][] = $logical_sensor->current_reading_corrected;
            });
        });

        /*
         * Calculate average value per logical sensor type and store in monitor
         */
        foreach ($monitor_values as $value_type => $values) {
            $value = round(array_sum(array_values($values)) / count($values), 2);
            $this->setProperty(
                PropertyTypesEnum::MONITORING_MONITOR_VALUE(), $value_type, $value, DatabaseDataTypesEnum::DATATYPE_FLOAT()
            );
        }

        /*
         * Delete properties of no longer existing reading types
         */
        $this->getProperties(PropertyTypesEnum::MONITORING_MONITOR_VALUE())->each(function (Property $p) use (&$monitor_values) {
            if (!in_array($p->name, array_keys($monitor_values))) {
                $p->delete();
            }
        });

        $this->unsetMonitorRefreshQueued()->save();

        return $this;
    }

    /**
     * @return Model
     */
    public function refreshMonitorHistory(): Model
    {
        if (is_null($bucket_size = $this->getProperty(PropertyTypesEnum::MONITOR_SETTING(), 'history_bucket_size_min'))) {
            $bucket_size = Setting::get('monitor_history_bucket_size_default_min', 5);
        }
        $bucket_size = (int)$bucket_size;

        if (is_null($time_frame = $this->getProperty(PropertyTypesEnum::MONITOR_SETTING(), 'history_time_frame_min'))) {
            $time_frame = Setting::get('monitor_history_time_frame_min', 360);
        }
        $time_frame = (int)$time_frame;

        $start = Carbon::now()->subMinutes($time_frame);
        $end = Carbon::now();

        $history = $this->getAverageByMetricInBucket($start, $end, $bucket_size);

        foreach ($history as $logical_sensor_type_id => $values) {
            $cache = [
                'values' => $values,
                'start' => $start->format('c'),
                'end' => $end->format('c'),
                'bucket_size_min' => $bucket_size
            ];

            $this->setProperty(
                PropertyTypesEnum::MONITORING_MONITOR_HISTORY_VALUE(), $logical_sensor_type_id, $cache, DatabaseDataTypesEnum::DATATYPE_JSON_ARRAY()
            );
        }

        return $this;
    }

    /**
     * @returns Model
     */
    public function setMonitorRefreshQueued(): Model
    {
        $this->is_monitor_refresh_queued = true;

        return $this;
    }

    /**
     * @returns Model
     */
    public function unsetMonitorRefreshQueued(): Model
    {
        $this->is_monitor_refresh_queued = false;
        $this->last_monitor_refresh_at = Carbon::now();

        return $this;
    }

    /**
     *
     */
    public function enrichMonitor(): void
    {
        if ($cache = Cache::get($this->monitorCacheKey())) {
            $this->_monitor = $cache;
        } else {
            $this->renderMonitor();
        }
    }

    /**
     *
     */
    public function renderMonitor(): void
    {
        $this->_monitor = [];

        $this->getProperties(PropertyTypesEnum::MONITORING_MONITOR_VALUE())->each(function (Property $p) {
            $type = LogicalSensorType::where('id', (int)$p->name)->select([
                'id', 'name', 'icon', 'reading_type_name', 'reading_type_unit', 'reading_type_symbol', 'reading_type_color'
            ])->first();

            $history = $this->getPropertyValue(PropertyTypesEnum::MONITORING_MONITOR_HISTORY_VALUE(), $p->name, []);
            $last_refresh = count($history) > 0 ? $this->getProperty(PropertyTypesEnum::MONITORING_MONITOR_HISTORY_VALUE(), $p->name)->updated_at : null;

            $this->_monitor[$type->name] = [
                'type' => $type,
                'logical_sensors' => $this->getLogicalSensors(['logical_sensor_type_id' => (int)$p->name], true),
                'value' => $p->value,
                'history' => $history,
                'last_refresh_at' => $last_refresh,
                'last_refresh_diff_minutes' => $last_refresh?->diffInMinutes(Carbon::now())
            ];
        });

        if (count($this->_monitor) < 1) $this->_monitor = null;

        Cache::put($this->monitorCacheKey(), $this->_monitor, $this->monitorCacheTtl());

        $this->requestFrontendRefresh();
    }

    /**
     * @return string
     */
    protected function monitorCacheKey(): string
    {
        return strtolower(sprintf('ciliatus.%s.%s.%s.monitor', $this::package(), $this::model(), $this->id));
    }

    /**
     * @return int
     */
    protected function monitorCacheTtl(): int
    {
        return config(strtolower(sprintf('ciliatus_monitoring.%s_%s_monitor_ttl', $this::package(), $this::model())));
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @param int $bucket_size
     * @param int $logical_sensor_type_id
     * @param int $range_lower_end
     * @param int $range_upper_end
     * @return bool
     */
    public function verifyHistoryForMetricStableInRange(
        Carbon $start,
        Carbon $end,
        int $bucket_size,
        int $logical_sensor_type_id,
        int $range_lower_end,
        int $range_upper_end): bool
    {
        $history = $this->getHistoryForMetric($start, $end, $bucket_size, $logical_sensor_type_id);
        if (count($history) == 0) return true;

        $out_of_range = array_filter($history, function ($bucket) use ($range_lower_end, $range_upper_end) {
            return ($range_lower_end && $bucket < $range_lower_end) ||
                ($range_upper_end && $bucket > $range_upper_end);
        });
        return count($out_of_range) == 0;
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @param int $bucket_size
     * @param int $logical_sensor_type_id
     * @return array|bool
     */
    public function getHistoryForMetric(
        Carbon $start,
        Carbon $end,
        int $bucket_size,
        int $logical_sensor_type_id): mixed
    {
        $logical_sensor_ids = $this->physical_sensors->map(function ($physical_sensor) use ($logical_sensor_type_id) {
            return $physical_sensor->logical_sensors->filter(function ($logical_sensor) use ($logical_sensor_type_id) {
                return $logical_sensor->logical_sensor_type_id == $logical_sensor_type_id;
            })->map(function ($logical_sensor) {
                return $logical_sensor->id;
            });
        })->flatten()->toArray();

        $result = $this->getAverageByMetricInBucket($start, $end, $bucket_size, $logical_sensor_ids);
        return isset($result[$logical_sensor_type_id]) ? $result[$logical_sensor_type_id] : [];
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @param int $bucket_size
     * @param array|null $logical_sensor_ids
     * @return array
     */
    private function getAverageByMetricInBucket(
        Carbon $start,
        Carbon $end,
        int $bucket_size,
        array $logical_sensor_ids = null): array
    {
        $cursor = clone $start;
        $logical_sensor_ids = $logical_sensor_ids ?? $this->physical_sensors->map(function ($physical_sensor) {
                return $physical_sensor->logical_sensors->map(function ($logical_sensor) {
                    return $logical_sensor->id;
                });
            })->flatten()->toArray();

        $logical_sensor_type_ids = LogicalSensor::whereIn('id', $logical_sensor_ids)->get()->map(function ($logical_sensor) {
            return $logical_sensor->logical_sensor_type_id;
        })->flatten()->unique()->toArray();

        $history = [];
        do {
            $bucket_starts_at = clone $cursor;
            $cursor->addMinutes($bucket_size);
            $bucket_ends_at = clone $cursor;

            $buckets = DB::table('ciliatus_monitoring__logical_sensor_readings')
                ->select(
                    DB::raw('ciliatus_monitoring__logical_sensors.logical_sensor_type_id AS logical_sensor_type_id'),
                    DB::raw('AVG(ciliatus_monitoring__logical_sensor_readings.reading_corrected) AS avg'))
                ->leftJoin('ciliatus_monitoring__logical_sensors', 'ciliatus_monitoring__logical_sensors.id', '=', 'ciliatus_monitoring__logical_sensor_readings.logical_sensor_id')
                ->whereIn('ciliatus_monitoring__logical_sensor_readings.logical_sensor_id', $logical_sensor_ids)
                ->where('ciliatus_monitoring__logical_sensor_readings.read_at', '>=', $bucket_starts_at)
                ->where('ciliatus_monitoring__logical_sensor_readings.read_at', '<=', $bucket_ends_at)
                ->groupBy('ciliatus_monitoring__logical_sensors.logical_sensor_type_id')
                ->get();

            $logical_sensor_types_found = [];
            foreach ($buckets as $bucket) {
                if (!isset($history[$bucket->logical_sensor_type_id])) $history[$bucket->logical_sensor_type_id] = [];
                $history[$bucket->logical_sensor_type_id][] = round($bucket->avg, 2);

                if (!isset($logical_sensor_types_found[$bucket->logical_sensor_type_id])) {
                    $logical_sensor_types_found[$bucket->logical_sensor_type_id] = true;
                }
            }

            // Fill empty buckets with null
            foreach ($logical_sensor_type_ids as $id) {
                if (isset($logical_sensor_types_found[$id])) continue;
                if (!isset($history[$id])) $history[$id] = [];
                $history[$id][] = null;
            }
        } while ($cursor->isBefore($end));

        return $history;
    }

}
