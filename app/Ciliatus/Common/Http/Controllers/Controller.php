<?php

namespace App\Ciliatus\Common\Http\Controllers;


use App\Ciliatus\Api\Http\Controllers\ControllerInterface;

abstract class Controller extends \Ciliatus\Api\Http\Controllers\Controller implements ControllerInterface
{

    /**
     * @var string
     */
    protected string $package = 'Common';
}
