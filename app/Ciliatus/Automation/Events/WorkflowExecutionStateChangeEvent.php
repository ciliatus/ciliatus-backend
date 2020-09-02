<?php

namespace App\Ciliatus\Automation\Events;

use App\Ciliatus\Automation\Enum\WorkflowExecutionStateEnum;
use App\Ciliatus\Common\Events\EventInterface;
use App\Ciliatus\Common\Models\Model;

class WorkflowExecutionStateChangeEvent extends Event implements EventInterface
{

    /**
     * @var WorkflowExecutionStateEnum
     */
    public WorkflowExecutionStateEnum $state;

    /**
     * @param Model $model
     * @param WorkflowExecutionStateEnum $state
     */
    public function __construct(Model $model, WorkflowExecutionStateEnum $state)
    {
        parent::__construct($model);
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'Automation.WorkflowExecutionStateChangeEvent';
    }

}
