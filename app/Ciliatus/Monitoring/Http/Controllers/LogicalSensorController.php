<?php

namespace App\Ciliatus\Monitoring\Http\Controllers;

use App\Ciliatus\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultIndexMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultShowMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultStoreMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultUpdateMethodTrait;
use App\Ciliatus\Monitoring\Models\LogicalSensor;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class LogicalSensorController extends Controller
{

    use UsesDefaultStoreMethodTrait,
        UsesDefaultIndexMethodTrait,
        UsesDefaultShowMethodTrait,
        UsesDefaultUpdateMethodTrait,
        UsesDefaultDestroyMethodTrait;

    /**
     * @param LogicalSensor $logicalSensor
     * @param string $start
     * @param string $end
     * @return JsonResponse
     */
    public function readings(LogicalSensor $logicalSensor, string $start, string $end) : JsonResponse
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);

        $bucket_size = round($start->diffInMinutes($end) / config('ciliatus_monitoring.max_buckets_sensorreadings'));
        $buckets = Collection::make();
        $cursor = clone $start;
        do {
            $next = (clone $cursor)->addMinutes($bucket_size);
            $readings = $logicalSensor->readings()->whereBetween('read_at', [$cursor, $next])->get()->map(fn($r) => $r->reading_corrected);
            $buckets->push([
                'time' => $cursor->format('c'),
                'value' =>$readings->avg()
            ]);
            $cursor = $next;
        } while ($cursor->lt($end));

        return $this->respondWithData($buckets->toArray());
    }

}
