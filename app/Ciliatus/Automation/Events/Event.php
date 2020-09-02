<?php

namespace App\Ciliatus\Automation\Events;

use App\Ciliatus\Common\Enum\BroadcastQueueEnum;
use App\Ciliatus\Common\Models\Model;

abstract class Event extends \Ciliatus\Common\Events\Event
{

    /**
     * @var string
     */
    public string $broadcastQueue;

    public function __construct(Model $model)
    {
        $this->broadcastQueue = BroadcastQueueEnum::QUEUE_AUTOMATION()->getValue();

        parent::__construct($model);
    }

}
