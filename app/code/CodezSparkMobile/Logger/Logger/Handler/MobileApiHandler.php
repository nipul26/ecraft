<?php
namespace CodezSparkMobile\Logger\Logger\Handler;

use Monolog\Logger;
use Magento\Framework\Logger\Handler\Base;

class MobileApiHandler extends Base
{
    /**
     * Logging level
     *
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     *
     * @var string
     */
    protected $fileName = '/var/log/mobile_api_logs.log';
}
