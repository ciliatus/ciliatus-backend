<?php

namespace App\Ciliatus\Monitoring\Traits;

use App\Ciliatus\Monitoring\Models\PhysicalSensor;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasSensorsTrait
{

    /**
     * @return MorphMany
     */
    public function physical_sensors(): MorphMany
    {
        return $this->morphMany(PhysicalSensor::class, 'belongsToModel');
    }

}
