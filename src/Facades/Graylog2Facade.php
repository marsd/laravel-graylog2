<?php
namespace AbsoluteSoftware\Graylog2\Facades;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class Graylog2Facade extends IlluminateFacade
{
    protected static function getFacadeAccessor()
    {
        return 'graylog2';
    }
}