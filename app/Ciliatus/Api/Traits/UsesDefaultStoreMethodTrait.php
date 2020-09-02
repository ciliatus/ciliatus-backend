<?php

namespace App\Ciliatus\Api\Traits;

use App\Ciliatus\Api\Http\Requests\Request;
use Illuminate\Http\JsonResponse;

trait UsesDefaultStoreMethodTrait
{

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function store(Request $request, int $id): JsonResponse
    {
        return $this->_store($request, $id);
    }

}
