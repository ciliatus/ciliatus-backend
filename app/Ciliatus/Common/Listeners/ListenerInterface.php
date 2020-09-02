<?php

namespace App\Ciliatus\Common\Listeners;

use App\Ciliatus\Common\Events\Event;

interface ListenerInterface
{

    public function handle(Event $event): void;

}
