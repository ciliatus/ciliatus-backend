<?php

namespace App\Ciliatus\Core\Http\Controllers;

use App\Ciliatus\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultIndexMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultShowMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultStoreMethodTrait;
use App\Ciliatus\Api\Traits\UsesDefaultUpdateMethodTrait;
use App\Ciliatus\Automation\Enum\WorkflowExecutionStateEnum;
use App\Ciliatus\Automation\Models\Appliance;
use App\Ciliatus\Automation\Models\ApplianceTypeState;
use App\Ciliatus\Automation\Models\WorkflowActionExecution;
use App\Ciliatus\Common\Enum\HttpStatusCodeEnum;
use App\Ciliatus\Common\Exceptions\ModelNotFoundException;
use App\Ciliatus\Common\Factory;
use App\Ciliatus\Core\Http\Requests\AgentCheckinRequest;
use App\Ciliatus\Core\Http\Requests\AgentLogRequest;
use App\Ciliatus\Core\Http\Requests\AgentReportActionStateRequest;
use App\Ciliatus\Core\Http\Requests\AgentReportApplianceStateRequest;
use App\Ciliatus\Core\Models\Agent;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class AgentController extends Controller
{

    use UsesDefaultStoreMethodTrait,
        UsesDefaultIndexMethodTrait,
        UsesDefaultShowMethodTrait,
        UsesDefaultUpdateMethodTrait,
        UsesDefaultDestroyMethodTrait;

    /**
     * Update client version and retrieve time offset between client and Ciliatus
     *
     * @param AgentCheckinRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws AuthorizationException
     */
    public function checkin__update(AgentCheckinRequest $request, int $id): JsonResponse
    {
        $this->auth();

        $agent = Factory::findOrFail(Agent::class, $id);
        $client_time = Carbon::parse($request->valid()->get('datetime'));

        $agent->updateClientInfo($client_time, $request->valid()->get('version'));

        $result = $this->respondWithModel($agent);

        $agent->last_checkin_at = Carbon::now();
        $agent->save();

        return $result;
    }

    /**
     * Claim possible workflow action executions
     *
     * @param int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws AuthorizationException
     */
    public function claim_update(int $id): JsonResponse
    {
        $this->auth();

        $agent = Factory::findOrFail(Agent::class, $id);

        $claimed = new Collection();
        WorkflowActionExecution::where('state', WorkflowExecutionStateEnum::STATE_WAITING())
            ->get()->each(function (WorkflowActionExecution $execution) use ($id, $agent, &$claimed) {
                if ($execution->action->appliance->agent_id == $id) {
                    $claimed->add($execution->claim($agent));
                }
            });

        $result = $this->respondWithModels($claimed);

        $agent->last_claim_at = Carbon::now();
        $agent->save();

        return $result;
    }

    /**
     * Retrieve Agent queued start times
     *
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ModelNotFoundException
     */
    public function start_times__show(int $id): JsonResponse
    {
        $this->auth();

        $agent = Factory::findOrFail(Agent::class, $id);

        $result = $this->respondWithModels($agent->ready_claimed_executions);

        $agent->last_start_times_at = Carbon::now();
        $agent->save();

        return $result;
    }

    /**
     * Retrieve Agent configuration
     *
     * @param int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws AuthorizationException
     */
    public function config__show(int $id): JsonResponse
    {
        $this->auth();

        $agent = Factory::findOrFail(Agent::class, $id);

        $result = $this->respondWithData([]);

        $agent->last_config_at = Carbon::now();
        $agent->save();

        return $result;
    }

    /**
     * Send Agent logs to Ciliatus
     *
     * @param AgentLogRequest $request
     * @param int $id
     * @throws AuthorizationException
     * @throws ModelNotFoundException
     */
    public function log__update(AgentLogRequest $request, int $id)
    {
        $this->auth();

        $agent = Factory::findOrFail(Agent::class, $id);
    }

    /**
     * @param AgentReportApplianceStateRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws AuthorizationException
     */
    public function report_appliance_state__update(AgentReportApplianceStateRequest $request, int $id): JsonResponse
    {
        $this->auth();

        /** @var Agent $agent */
        $agent = Factory::findOrFail(Agent::class, $id);
        /** @var Appliance $appliance */
        $appliance = Factory::findOrFail(Appliance::class, $request->valid()->get('appliance_id'));

        if (!$appliance->isControlledBy($agent)) {
            return $this->respondWithError(
                sprintf('Agent %s has no control over Appliance %s', $id, $appliance->id),
                HttpStatusCodeEnum::Unprocessable_Entity()
            );
        }

        /** @var ApplianceTypeState $state */
        $state = Factory::findOrFail(ApplianceTypeState::class, $request->valid()->get('state_id'));
        $appliance->setState($state);

        return $this->respondWithModel($appliance);
    }

    /**
     * @param AgentReportActionStateRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws AuthorizationException
     */
    public function report_action_state__update(AgentReportActionStateRequest $request, int $id): JsonResponse
    {
        $this->auth();

        /** @var Agent $agent */
        $agent = Factory::findOrFail(Agent::class, $id);
        /** @var WorkflowActionExecution $action */
        $action_execution = Factory::findOrFail(WorkflowActionExecution::class, $request->valid()->get('action_execution_id'));

        if (!$action_execution->isClaimedBy($agent)) {
            return $this->respondWithError(
                sprintf('Agent %s has no control over Action Execution %s', $id, $action_execution->id),
                HttpStatusCodeEnum::Unprocessable_Entity()
            );
        }

        $action_execution->setStatus(
            WorkflowExecutionStateEnum::search($request->valid()->get('status')),
            $request->valid()->get('status_text')
        );

        return $this->respondWithModel($action_execution);
    }

}
