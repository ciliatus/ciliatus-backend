<?php

namespace App\Ciliatus\Core\Http\Requests;

use App\Ciliatus\Core\Models\Habitat;
use Illuminate\Validation\Rule;

class UpdateHabitatRequest extends Request
{

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('ciliatus_core__habitats', 'name')->ignore(Habitat::find($this->id)->name, 'name')
            ],
            'relations.habitat_type' => 'required',
            'relations.location' => 'required',
            'relations.*' => ''
        ];
    }

}
