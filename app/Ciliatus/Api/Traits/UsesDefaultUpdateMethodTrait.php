<?php

namespace App\Ciliatus\Api\Traits;

use App\Ciliatus\Api\Http\Requests\Request;
use Illuminate\Http\JsonResponse;

trait UsesDefaultUpdateMethodTrait
{

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->_update($request, $id);
    }

}
