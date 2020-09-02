<?php

namespace App\Ciliatus\Core\Http\Requests;

use App\Ciliatus\Api\Http\Requests\Request;

class AgentCheckinRequest extends Request
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'datetime' => 'required|date_format:c',
            'version' => 'required|string'
        ];
    }

}
