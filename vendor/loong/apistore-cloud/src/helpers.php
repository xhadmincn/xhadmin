<?php
if (!function_exists('yd_env')) {
    function yd_env($key, $default = null)
    {
        if (function_exists('env')) {
            return env($key, $default);
        }
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        return $_ENV[$key] ?? $default;
    }
}
