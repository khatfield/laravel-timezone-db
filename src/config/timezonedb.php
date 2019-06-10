<?php

return [
    'api_key'     => env('TIMEZONEDB_API_KEY'),
    'premium'     => env('TIMEZONEDB_PREMIUM', false),
    'log_channel' => env('TIMEZONEDB_LOG_CHANNEL', 'daily'),
];