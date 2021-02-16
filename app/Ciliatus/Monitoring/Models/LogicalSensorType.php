<?php

namespace App\Ciliatus\Monitoring\Models;

use App\Ciliatus\Common\Models\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogicalSensorType extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'icon',
        'reading_minimum', 'reading_maximum',
        'reading_type_name', 'reading_type_unit', 'reading_type_symbol', 'reading_type_color'
    ];

    /**
     * @return bool|null
     * @throws \Exception
     */
    public function delete(): ?bool
    {
        $this->logical_sensors()->delete();
        return parent::delete();
    }

    /**
     * @return HasMany
     */
    public function logical_sensors(): HasMany
    {
        return $this->hasMany(LogicalSensor::class);
    }

}
