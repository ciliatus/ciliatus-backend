<?php

namespace App\Ciliatus\Automation\Models;

use App\Ciliatus\Automation\Enum\ApplianceGroupStateEnum;
use App\Ciliatus\Common\Models\Model;
use App\Ciliatus\Core\Models\Habitat;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class ApplianceGroup extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'is_active', 'is_builtin', 'state', 'state_text'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_builtin' => 'boolean'
    ];

    /**
     * @var array
     */
    protected $with = [
        'appliances', 'capabilities'
    ];

    /**
     * @return BelongsToMany
     */
    public function appliances(): BelongsToMany
    {
        return $this->belongsToMany(
            Appliance::class,
            'ciliatus_automation__appliance_appliance_group_pivot',
            'appliance_group_id',
            'appliance_id'
        );
    }

    /**
     * @return BelongsToMany
     */
    public function capabilities(): BelongsToMany
    {
        return $this->belongsToMany(
            Capability::class,
            'ciliatus_automation__appliance_group_capability_pivot',
            'appliance_group_id',
            'capability_id'
        );
    }

    /**
     * @return MorphToMany
     */
    public function habitats(): MorphToMany
    {
        return $this->morphedByMany(
            Habitat::class,
            'belongsToModel',
            'ciliatus_automation__appliance_group_belongs_pivot'
        );
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon ?? '';
    }

    /**
     * @param string $text
     * @return ApplianceGroup
     */
    public function error(string $text): self
    {
        $this->state = ApplianceGroupStateEnum::STATE_ERROR();
        $this->state_text = $text;
        $this->save();

        return $this;
    }

}
