<?php

namespace App\Ciliatus\Automation\Models;

use App\Ciliatus\Common\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplianceTypeState extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'icon', 'is_appliance_on', 'has_level', 'appliance_type_id'
    ];

    protected $casts = [
        'has_level' => 'boolean'
    ];

    /**
     * @return BelongsTo
     */
    public function appliance_type(): BelongsTo
    {
        return $this->belongsTo(ApplianceType::class, 'appliance_type_id');
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon ?? '';
    }

}
