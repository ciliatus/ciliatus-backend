<?php

namespace App\Providers;

use App\Ciliatus\Automation\Events\ApplianceErrorEvent;
use App\Ciliatus\Automation\Events\ApplianceRecoveryEvent;
use App\Ciliatus\Automation\Events\WorkflowExecutionRuntimeExceededEvent;
use App\Ciliatus\Automation\Listeners\ApplianceErrorEventListener;
use App\Ciliatus\Automation\Listeners\WorkflowRuntimeExceededEventListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;

class CiliatusAutomationEventServiceProvider extends EventServiceProvider
{

    /**
     * @var array
     */
    protected $listen = [
        ApplianceErrorEvent::class => [
            ApplianceErrorEventListener::class
        ],

        ApplianceRecoveryEvent::class => [

        ],

        WorkflowExecutionRuntimeExceededEvent::class => [
            WorkflowRuntimeExceededEventListener::class
        ]
    ];

}
