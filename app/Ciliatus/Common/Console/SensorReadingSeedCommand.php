<?php

namespace App\Ciliatus\Common\Console;

use App\Ciliatus\Monitoring\Models\LogicalSensor;
use App\Ciliatus\Monitoring\Models\LogicalSensorType;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class SensorReadingSeedCommand extends Command
{

    /**
     * @var string
     */
    protected $signature = 'ciliatus:sensor_reading_seed {--minutes=1440}';

    /**
     * @var string
     */
    protected $description = 'Seed sensor reading test data';

    /**
     * @throws Exception
     */
    public function handle()
    {
        $types = [
            'temperature' => [
                'unit' => 'celsius',
                'base' => 20,
                'factor' => 1,
                'random' => [-0.1, 0.1]
            ],
            'humidity' => [
                'unit' => 'percent',
                'base' => 50,
                'factor' => 6,
                'random' => [-0.5, 0.5]
            ]
        ];

        $now = Carbon::now();
        $start = (clone $now)->subMinutes((int)$this->option('minutes'));

        foreach ($types as $type_name => $settings) {
            $type = LogicalSensorType::where('name', trans('ciliatus.monitoring::logical_sensor.name.' . $type_name))->first();
            $type->logical_sensors->each(function (LogicalSensor $sensor) use ($now, $start, $settings) {
                echo 'Seeding ' . $sensor->name . ' ... ';
                $sensor->enterBatchMode();

                $cursor = clone $start;
                do {
                    $diff = $cursor->diffInMinutes((clone $cursor)->startOfDay());
                    $variable = cos($diff / 720 * pi());
                    $value = $settings['base'] + $variable * $settings['factor'] + random_int($settings['random'][0] * 100, $settings['random'][1] * 100) / 100;
                    $sensor->addReading($value, $cursor);
                    $cursor->addMinutes(10);
                } while ($cursor->isBefore($now));

                $sensor->endBatchMode();
                echo 'done' . PHP_EOL;
            });
        }

        echo "Seeding done" . PHP_EOL;
    }

}
