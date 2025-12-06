<?php
namespace CodezSparkMobile\Logger\Logger;

use Monolog\Logger as MonologLogger;

class Logger extends MonologLogger
{
    public function __construct(
        string $name = 'mobile_logger',
        array $handlers = [],
        array $processors = []
    ) {
        parent::__construct($name, $handlers, $processors);
    }
}
