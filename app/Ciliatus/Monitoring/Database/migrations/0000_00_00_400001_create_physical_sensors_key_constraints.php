<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhysicalSensorsKeyConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ciliatus_monitoring__physical_sensors', function (Blueprint $table) {
            $table->foreign('agent_id', 'physical_sensors__agent_foreign')
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
        Schema::table('ciliatus_monitoring__physical_sensors', function (Blueprint $table) {
            $table->dropForeign('agent_id');
        });
    }
}
