<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplianceGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ciliatus_automation__appliance_groups', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->string('state')->nullable()->default('ok');
            $table->string('state_text')->nullable()->default('');
            $table->boolean('is_active')->nullable()->default(true);
            $table->boolean('is_builtin')->nullable()->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ciliatus_automation__appliance_groups');
    }
}
