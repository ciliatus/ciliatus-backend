<?php

namespace App\Ciliatus\Core\Http\Requests;

use App\Ciliatus\Api\Http\Requests\Request;
use App\Ciliatus\Automation\Http\Requests\Rules\WorkflowActionExecutionStateRule;

class AgentReportActionStateRequest extends Request
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'state' => ['string', 'required', new WorkflowActionExecutionStateRule()],
            'action_execution_id' => 'int|required'
        ];
    }

}
