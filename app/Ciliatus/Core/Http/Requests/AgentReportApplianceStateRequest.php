<?php

namespace App\Ciliatus\Core\Http\Requests;

use App\Ciliatus\Api\Http\Requests\Request;

class AgentReportApplianceStateRequest extends Request
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'state_id' => 'int|required',
            'appliance_id' => 'int|required'
        ];
    }

}
