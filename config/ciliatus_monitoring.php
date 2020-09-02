<?php

return [

    /*
     * Monitor Cache TTLs in seconds
     * Defines how long a model's monitor stays in cache
     * until the background service has to refresh it
     */
    'core_habitat_monitor_ttl'  => env('CILIATUS_MONITORING__CORE_HABITAT_MONITOR_TTL', 120),
    'core_location_monitor_ttl' => env('CILIATUS_MONITORING__CORE_LOCATION_MONITOR_TTL', 120)

];
