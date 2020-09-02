<?php

namespace App\Ciliatus\Automation\Http\Requests;

use App\Ciliatus\Api\Http\Requests\Request;
use App\Ciliatus\Automation\Http\Requests\Rules\HealthIndicatorStatusRule;

class ApplianceHealthRequest extends Request
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type_id' => 'int|required',
            'message' => 'string|required',
            'state' => ['string', 'required', new HealthIndicatorStatusRule()],
            'state_text' => 'string',
            'value' => 'float'
        ];
    }

}
