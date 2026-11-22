<?php

class Logger
{
    public static function info(string $message): void
    {
        self::write('info.log', 'INFO', $message);
    }

    public static function error(string $message): void
    {
        self::write('error.log', 'ERROR', $message);
    }

    private static function write(string $file, string $level, string $message): void
    {
        $line = '[' . date('Y-m-d H:i:s') . "] $level $message" . PHP_EOL;
        file_put_contents(__DIR__ . '/../logs/' . $file, $line, FILE_APPEND | LOCK_EX);
    }
}
