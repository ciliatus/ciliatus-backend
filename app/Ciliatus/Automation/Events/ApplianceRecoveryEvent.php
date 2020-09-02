<?php

namespace App\Ciliatus\Automation\Events;

use App\Ciliatus\Common\Events\EventInterface;

class ApplianceRecoveryEvent extends Event implements EventInterface
{

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'Automation.ApplianceRecoveryEvent';
    }

}
