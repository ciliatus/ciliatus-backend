<?php

namespace App\Ciliatus\Core\Models;

use App\Ciliatus\Automation\Traits\HasAppliancesTrait;
use App\Ciliatus\Common\Models\Model;
use App\Ciliatus\Monitoring\Traits\HasMonitorTrait;
use App\Ciliatus\Monitoring\Traits\HasSensorsTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{

    use HasAppliancesTrait, HasSensorsTrait, HasMonitorTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'location_type_id'
    ];

    /**
     * @var array
     */
    protected ?array $transformable = [
        'name', 'location_type_id',
        '_monitor'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'last_monitor_refresh_at'
    ];

    protected $with = [
        'location_type'
    ];

    /**
     * @return BelongsTo
     */
    public function location_type(): BelongsTo
    {
        return $this->belongsTo(LocationType::class, 'location_type_id');
    }

    /**
     * @return HasMany
     */
    public function habitats(): HasMany
    {
        return $this->hasMany(Habitat::class, 'location_id');
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return 'mdi-map-marker-outline';
    }

    /**
     * @return Model
     */
    public function enrich(): Model
    {
        $this->enrichMonitor();

        return parent::enrich();
    }

}
