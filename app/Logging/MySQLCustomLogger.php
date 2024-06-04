<?php
namespace App\Logging;
use Monolog\Logger;
use App\logging\MySQLLoggingHandler;

class MySQLCustomLogger{
/**
     * Create a custom Monolog instance.
     *
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger("MySQLLoggingHandler");
        return $logger->pushHandler(new MySQLLoggingHandler());
    }
}
