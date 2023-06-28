<?php

namespace App\Loggers;

interface ApiLoggerInterface
{
    public function info(string $message): void;
}
