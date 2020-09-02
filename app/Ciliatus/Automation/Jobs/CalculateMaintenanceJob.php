<?php

namespace App\Ciliatus\Automation\Jobs;

use App\Ciliatus\Automation\Models\Appliance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateMaintenanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Appliance $appliance
     */
    protected $appliance;

    /**
     * @param Appliance $appliance
     */
    public function __construct(Appliance $appliance)
    {
        $this->appliance = $appliance;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $this->appliance->calculateMaintenance();
        $this->appliance->save();
    }
}
