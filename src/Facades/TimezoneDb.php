<?php


namespace Khatfield\LaravelTimezoneDb\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * Class TimezoneDb
 * @package Khatfield\LaravelTimezoneDb\Facades
 *
 * @method static Collection getTimezoneByCity(string $city, string $country, string|null $region = null)
 */
class TimezoneDb extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'timezonedb';
    }
}