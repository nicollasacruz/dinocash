<?php

namespace App\Logging;

use Illuminate\Log\Logger;
use Illuminate\Support\Arr;
use Monolog\Formatter\LineFormatter;

class CustomizeFormatter
{
    /**
     * Customize the given logger instance.
     *
     * @param  Logger  $logger
     *
     * @return void
     */
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor(function ($record) {
                return Arr::add($record, 'prefix', env('APP_URL'));
            });
            $handler->setFormatter(tap(new LineFormatter(
                "[%datetime%] %prefix%.%channel%.%level_name%: %message% %context% %extra%\n",
                'Y-m-d H:i:s',
                true,
                true
            ), function ($formatter) {
                $formatter->includeStacktraces();
            }));
        }
    }
}