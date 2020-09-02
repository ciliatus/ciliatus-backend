<?php

namespace App\Ciliatus\Automation\Http\Requests;

use App\Ciliatus\Api\Http\Requests\Request;

class ApplianceErrorRequest extends Request
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'message' => 'string|required'
        ];
    }

}
