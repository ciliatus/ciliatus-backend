<?php

namespace App\Ciliatus\Monitoring\Traits;

use App\Ciliatus\Monitoring\Models\PhysicalSensor;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

trait HasSensorsTrait
{

    /**
     * @return MorphMany
     */
    public function physical_sensors(): MorphMany
    {
        return $this->morphMany(PhysicalSensor::class, 'belongsToModel');
    }

    /**
     * @param bool $transform
     * @return Collection
     */
    public function getLogicalSensors($filter = [], $transform = false): Collection
    {
        $logical_sensors = Collection::make();
        foreach ($this->physical_sensors as $ps) {
            $q = $ps->logical_sensors();
            foreach ($filter as $k=>$v) {
                if (is_array($v)) {
                    $q->where($k, $v[0], $v[1]);
                } else {
                    $q->where($k, $v);
                }
            }
            foreach ($q->get() as $ls) {
                if ($transform) $ls = $ls->transform();
                $logical_sensors->push($ls);
            }
        }

        return $logical_sensors;
    }

}
