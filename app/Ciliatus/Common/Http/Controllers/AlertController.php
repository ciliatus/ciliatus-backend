<?php

namespace App\Ciliatus\Common\Http\Controllers;

use App\Ciliatus\Api\Traits\UsesDefaultIndexMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultShowMethodTrait;
use App\Ciliatus\Common\Exceptions\ModelNotFoundException;
use App\Ciliatus\Common\Factory;
use App\Ciliatus\Common\Http\Requests\AcknowledgeAlertRequest;
use App\Ciliatus\Common\Models\Alert;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class AlertController extends Controller
{

    use UsesDefaultIndexMethodTrait,
        UsesDefaultShowMethodTrait;

    /**
     * @param AcknowledgeAlertRequest $request
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws AuthorizationException
     */
    public function acknowledge__update(AcknowledgeAlertRequest $request): JsonResponse
    {
        $this->auth();

        $alert = Factory::findOrFail(Alert::class, $request->valid()->id);
        $alert->ack();

        return $this->respondWithModel($alert);
    }

}
