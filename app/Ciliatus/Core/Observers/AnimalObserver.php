<?php

namespace App\Ciliatus\Core\Observers;


use App\Ciliatus\Core\Enum\AnimalHistoryEventsEnum;
use App\Ciliatus\Core\Models\Animal;

class AnimalObserver
{

    /**
     * @param Animal $animal
     */
    public function created(Animal $animal): void
    {
        $animal->writeHistory(AnimalHistoryEventsEnum::ANIMAL_CREATED());
    }
}
