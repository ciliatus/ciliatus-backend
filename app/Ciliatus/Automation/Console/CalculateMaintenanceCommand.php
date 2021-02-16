<?php

namespace App\Ciliatus\Automation\Console;

use App\Ciliatus\Automation\Jobs\CalculateMaintenanceJob;
use App\Ciliatus\Automation\Models\Appliance;
use Illuminate\Console\Command;

class CalculateMaintenanceCommand extends Command
{

    /**
     * @var string
     */
    protected $signature = 'ciliatus:automation.calculate_maintenance';

    /**
     * @var string
     */
    protected $description = 'Calculates next appliance maintenances';

    /**
     *
     */
    public function handle()
    {
        Appliance::get()->each(function (Appliance $appliance) {
            dispatch(new CalculateMaintenanceJob($appliance));
        });
    }

}
