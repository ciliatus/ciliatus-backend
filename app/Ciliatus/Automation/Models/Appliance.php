<?php

namespace App\Ciliatus\Automation\Models;

use App\Ciliatus\Automation\Enum\ApplianceStateEnum;
use App\Ciliatus\Automation\Events\ApplianceErrorEvent;
use App\Ciliatus\Common\Models\Model;
use App\Ciliatus\Common\Traits\HasAlertsTrait;
use App\Ciliatus\Common\Traits\HasHealthIndicatorTrait;
use App\Ciliatus\Core\Models\Agent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Appliance extends Model
{

    use HasAlertsTrait;
    use HasHealthIndicatorTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'is_active', 'appliance_type_id', 'state', 'state_text', 'agent_id',
        'maintenance_interval_days', 'next_maintenance_due_days',
        'last_maintenance_at', 'next_maintenance_due_at', 'is_active_on_conflict'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * @var array
     */
    protected $with = [
        'appliance_type', 'current_state'
    ];

    /**
     * @return BelongsTo
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * @return BelongsTo
     */
    public function appliance_type(): BelongsTo
    {
        return $this->belongsTo(ApplianceType::class, 'appliance_type_id');
    }

    /**
     * @return BelongsTo
     */
    public function current_state(): BelongsTo
    {
        return $this->belongsTo(ApplianceTypeState::class, 'current_state_id');
    }

    /**
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            ApplianceGroup::class,
            'ciliatus_automation__appliance_appliance_group_pivot',
            'appliance_id',
            'appliance_group_id'
        );
    }

    /**
     * @return HasMany
     */
    public function actions(): HasMany
    {
        return $this->hasMany(WorkflowAction::class, 'appliance_id');
    }

    /**
     * @return HasManyThrough
     */
    public function capabilities(): HasManyThrough
    {
        return $this->hasManyThrough(Capability::class, ApplianceType::class);
    }

    /**
     * @param ApplianceTypeState $state
     * @return Appliance
     */
    public function setState(ApplianceTypeState $state = null): self
    {
        if (is_null($state)) {
            return $this->error(trans('ciliatus.automation::appliance.state_text.error.null_state'));
        }

        if (!$this->appliance_type->states->map(function ($s) {
            return $s->id;
        })->contains($state->id)) {
            return $this->error(trans('ciliatus.automation::appliance.state_text.error.invalid_state'));
        }

        $this->current_state()->associate($state);
        $this->state = $state->is_appliance_on ? ApplianceStateEnum::STATE_ACTIVE() : ApplianceStateEnum::STATE_INACTIVE();

        return $this;
    }

    /**
     * @param string $text
     * @return Appliance
     */
    public function error(string $text): self
    {
        $this->current_state()->dissociate();
        $this->state = ApplianceStateEnum::STATE_ERROR();
        $this->state_text = $text;
        $this->save();

        event(new ApplianceErrorEvent($this, $text));

        return $this;
    }

    /**
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->state != ApplianceStateEnum::STATE_ERROR();
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->appliance_type->icon;
    }

    /**
     *
     */
    public function calculateMaintenance(): void
    {
        if (is_null($this->maintenance_interval_days)) {
            $this->next_maintenance_due_at = null;
            $this->next_maintenance_due_days = null;
            return;
        };

        $base = $this->last_maintenance_at ?? $this->created_at;
        $this->next_maintenance_due_at = $base->addDays($this->maintenance_interval_days);
        $this->next_maintenance_due_days = $base->diffInDays($this->maintenance_interval_days);
    }

    /**
     * @param Agent $agent
     * @return bool
     */
    public function isControlledBy(Agent $agent): bool
    {
        return $this->agent_id == $agent->id;
    }

}
