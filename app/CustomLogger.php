<?php

namespace App;

use Monolog\Logger;
use Monolog\Handler\LogEntriesHandler;

class CustomLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
      $loggerHandler = new LogEntriesHandler(env('LOGENTRIES_TOKEN'));
      return new Logger('logentries',[$loggerHandler]);
    }
}
