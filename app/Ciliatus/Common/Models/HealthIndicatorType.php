<?php

namespace App\Ciliatus\Common\Models;

use App\Ciliatus\Common\Traits\HasMultipleDataTypeFieldsTrait;

class HealthIndicatorType extends Model
{

    use HasMultipleDataTypeFieldsTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'icon'
    ];

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon ?? 'help';
    }

}
