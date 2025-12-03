<?php

/**
 * 简单文件日志工具
 */
class Log
{
    private static string $logDir = '/var/log/cmx-ranking';

    public static function info(string $type, array $data = []): void
    {
        self::write('INFO', $type, $data);
    }

    public static function error(string $type, array $data = []): void
    {
        self::write('ERROR', $type, $data);
    }

    private static function write(string $level, string $type, array $data): void
    {
        if (!is_dir(self::$logDir)) {
            @mkdir(self::$logDir, 0770, true);
        }

        $line = sprintf(
            "[%s][%s][%s] %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $type,
            json_encode($data, JSON_UNESCAPED_UNICODE)
        );

        $file = self::$logDir . '/cmx-' . date('Y-m-d') . '.log';
        @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }
}


