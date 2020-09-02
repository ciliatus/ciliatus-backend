<?php

namespace App\Ciliatus\Automation\Enum;

use App\Ciliatus\Common\Enum\Enum;

/**
 * @method static ApplianceGroupStateEnum STATE_OK
 * @method static ApplianceGroupStateEnum STATE_ERROR
 */
class ApplianceGroupStateEnum extends Enum
{

    private const STATE_OK = 'ok';
    private const STATE_ERROR = 'error';

}
