<?php

namespace Support\Logging\Telegram;

use Monolog\Logger;

class TelegramLoggerFactory
{
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('channels.telegram');
        $logger->pushHandler(new TelegramLoggerHandler($config));

        return $logger;
    }
}
