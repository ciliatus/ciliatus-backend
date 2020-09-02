<?php

namespace App\Ciliatus\Core\Http\Controllers;

use App\Ciliatus\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultIndexMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultShowMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultStoreMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultUpdateMethodTrait;

class AnimalController extends Controller
{

    use UsesDefaultStoreMethodTrait,
        UsesDefaultIndexMethodTrait,
        UsesDefaultShowMethodTrait,
        UsesDefaultUpdateMethodTrait,
        UsesDefaultDestroyMethodTrait;
}
