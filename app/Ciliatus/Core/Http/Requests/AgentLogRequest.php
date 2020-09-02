<?php

namespace App\Ciliatus\Core\Http\Requests;

use App\Ciliatus\Api\Http\Requests\Request;

class AgentLogRequest extends Request
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'log' => 'required|json'
        ];
    }

}
