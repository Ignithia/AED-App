<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

final class Database
{
    private static ?self $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $env = $this->loadEnv(dirname(__DIR__) . '/.env');

        $host = $env['DB_HOST'] ?? '127.0.0.1';
        $dbName = $env['DB_NAME'] ?? 'aed_app';
        $username = $env['DB_USER'] ?? 'root';
        $password = $env['DB_PASS'] ?? '';
        $port = (int) ($env['DB_PORT'] ?? 3306);
        $charset = $env['DB_CHARSET'] ?? 'utf8mb4';

        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $dbName, $charset);

        try {
            // Debug: print info if connection fails
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            $msg = sprintf(
                "Database connection failed. Host: %s, User: %s, DB: %s. Error: %s",
                $host,
                $username,
                $dbName,
                $exception->getMessage()
            );
            throw new PDOException($msg, (int) $exception->getCode(), $exception);
        }
    }

    /**
     * @return array<string, string>
     */
    private function loadEnv(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env = [];

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $env[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
        }

        return $env;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}