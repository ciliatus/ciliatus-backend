<?php

namespace App\Ciliatus\Automation\Http\Controllers;

use App\Ciliatus\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultIndexMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultShowMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultStoreMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultUpdateMethodTrait;
use App\Ciliatus\Automation\Http\Requests\ApplianceErrorRequest;
use App\Ciliatus\Automation\Http\Requests\ApplianceHealthRequest;
use App\Ciliatus\Automation\Models\Appliance;
use App\Ciliatus\Common\Enum\HealthIndicatorStatusEnum;
use App\Ciliatus\Common\Exceptions\ModelNotFoundException;
use App\Ciliatus\Common\Factory;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class ApplianceController extends Controller
{

    use UsesDefaultStoreMethodTrait,
        UsesDefaultIndexMethodTrait,
        UsesDefaultShowMethodTrait,
        UsesDefaultUpdateMethodTrait,
        UsesDefaultDestroyMethodTrait;

    /**
     * @param ApplianceErrorRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws AuthorizationException
     */
    public function error__update(ApplianceErrorRequest $request, int $id): JsonResponse
    {
        $this->auth();

        $appliance = Factory::findOrFail(Appliance::class, $id);

        return $this->respondWithModel($appliance->error($request->valid()->get('message')));
    }

    /**
     * @param ApplianceHealthRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws AuthorizationException
     */
    public function health__show(ApplianceHealthRequest $request, int $id): JsonResponse
    {
        $this->auth();

        $appliance = Factory::findOrFail(Appliance::class, $id);
        $enum_key = HealthIndicatorStatusEnum::search('type_id');
        $indicator = $appliance->setHealthIndicator(
            $request->valid()->get('state'),
            $request->valid()->get('name'),
            HealthIndicatorStatusEnum::$enum_key(),
            $request->valid()->get('state_text'),
            $request->valid()->get('value')
        );

        return $this->respondWithModel($indicator);
    }

}
