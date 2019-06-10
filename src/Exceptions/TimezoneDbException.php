<?php


namespace Khatfield\LaravelTimezoneDb\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class TimezoneDbException extends Exception
{
    public function report()
    {
        $channel = config('timezonedb.log_channel');

        Log::channel($channel)->error($this->getMessage());
    }
}