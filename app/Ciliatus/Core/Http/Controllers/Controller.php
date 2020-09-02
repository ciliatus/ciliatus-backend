<?php

namespace App\Ciliatus\Core\Http\Controllers;


use App\Ciliatus\Api\Http\Controllers\ControllerInterface;

abstract class Controller extends \App\Ciliatus\Api\Http\Controllers\Controller implements ControllerInterface
{

    /**
     * @var string
     */
    protected string $package = 'Core';
}
