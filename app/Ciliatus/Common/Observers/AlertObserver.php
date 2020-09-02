<?php

namespace App\Ciliatus\Common\Observers;


use App\Ciliatus\Common\Enum\AlertEventsEnum;
use App\Ciliatus\Common\Events\AlertStartedEvent;
use App\Ciliatus\Common\Models\Alert;
use App\Ciliatus\Common\Traits\HasHistoryTrait;
use ReflectionObject;

class AlertObserver
{

    /**
     * @param Alert $alert
     */
    public function created(Alert $alert)
    {
        if (in_array(HasHistoryTrait::class, (new ReflectionObject($alert))->getTraitNames())) {
            $alert->writeHistory(AlertEventsEnum::ALERT_STARTED(), [], $alert->started_at);
        }

        event(new AlertStartedEvent($alert));
    }

}
