<?php
class database
{
    protected ?PDO $conn = null;
    public function __construct() {
        // Demo records stay in the visitor's session.
        if (getenv('APP_MODE') !== 'database') {
            return;
        }
        $password = getenv('DB_PASSWORD');
        if ($password === false || $password === '') {
            throw new RuntimeException('Database password is not configured.');
        }
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $name = getenv('DB_NAME') ?: 'student_portal';
        $user = getenv('DB_USER') ?: 'student_portal';
        $this->conn = new PDO("mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4", $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}
