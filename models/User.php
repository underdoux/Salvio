<?php
require_once __DIR__ . '/../core/Database.php';

class User
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function findByUsername(string $username)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = :username AND deleted_at IS NULL LIMIT 1');
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    // Additional CRUD methods can be added here
}
?>
