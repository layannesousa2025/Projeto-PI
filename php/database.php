<?php

function champions_database_config(): array
{
    static $config;

    if ($config === null) {
        $configFile = __DIR__ . '/config.local.php';
        if (!is_file($configFile)) {
            throw new RuntimeException(
                'Database configuration is missing. Copy php/config.example.php to php/config.local.php and set the database credentials.'
            );
        }

        $config = require $configFile;
        if (!is_array($config)) {
            throw new RuntimeException('Database configuration must return an array.');
        }

        foreach (['host', 'username', 'password', 'database', 'port'] as $key) {
            if (
                !array_key_exists($key, $config)
                || !is_scalar($config[$key])
                || ($key !== 'password' && trim((string) $config[$key]) === '')
            ) {
                throw new RuntimeException('Database configuration is invalid: missing ' . $key . '.');
            }
        }
    }

    return $config;
}

function champions_mysqli(): mysqli
{
    $config = champions_database_config();
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $connection = new mysqli(
        $config['host'],
        $config['username'],
        $config['password'],
        $config['database'],
        (int) $config['port']
    );
    $connection->set_charset('utf8mb4');

    return $connection;
}

function champions_pdo(): PDO
{
    $config = champions_database_config();

    return new PDO(
        sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $config['host'],
            (int) $config['port'],
            $config['database']
        ),
        $config['username'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
}
