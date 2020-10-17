<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkflowExecutionsKeyConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ciliatus_automation__workflow_action_executions', function (Blueprint $table) {
            $table->foreign('claimed_by_agent_id', 'workflow_action_executions__agent_foreign')
                ->references('id')
                ->on('ciliatus_core__agents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ciliatus_automation__workflow_action_executions', function (Blueprint $table) {
            $table->dropForeign('claimed_by_agent_id');
        });
    }
}
