<?php

return [
    'model' => 'App\Ciliatus\Monitoring\Models\LogicalSensorType',
    'items' => [
        [
            'name' => trans('ciliatus.monitoring::logical_sensor.name.temperature'),
            'icon' => 'mdi-thermometer',
            'reading_minimum' => -100,
            'reading_maximum' => 100,
            'reading_type_name' => 'temperature',
            'reading_type_unit' => 'celsius',
            'reading_type_symbol' => '°C',
            'reading_type_color' => 'f43f33'
        ],
        [
            'name' => trans('ciliatus.monitoring::logical_sensor.name.humidity'),
            'icon' => 'mdi-water-percent',
            'reading_minimum' => 0,
            'reading_maximum' => 100,
            'reading_type_name' => 'humidity',
            'reading_type_unit' => 'percent',
            'reading_type_symbol' => '%',
            'reading_type_color' => '42b3f4'
        ],
        [
            'name' => trans('ciliatus.monitoring::logical_sensor.name.light_intensity'),
            'icon' => 'mdi-lightbulb-on-outline',
            'reading_minimum' => 0,
            'reading_maximum' => 1000000,
            'reading_type_name' => 'light_intensity',
            'reading_type_unit' => 'lumen',
            'reading_type_symbol' => 'lm',
            'reading_type_color' => 'f4b342'
        ],
    ]
];
