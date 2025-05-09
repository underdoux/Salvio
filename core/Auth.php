<?php
// Authentication and role-based access control for Salvio POS system

class Auth
{
    public static function check()
    {
        return isset($_SESSION['user']);
    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function login(array $user)
    {
        $_SESSION['user'] = $user;
        session_regenerate_id(true);
    }

    public static function logout()
    {
        unset($_SESSION['user']);
        session_destroy();
    }

    public static function checkRole(string $role)
    {
        if (!self::check()) {
            return false;
        }
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $role;
    }

    public static function requireRole(string $role)
    {
        if (!self::checkRole($role)) {
            header('Location: /login');
            exit;
        }
    }
}
?>
