<?php
// Secure session management for Salvio POS system

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name('salvio_session');
            session_start();
            // Set secure cookie parameters
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                session_set_cookie_params([
                    'lifetime' => $params['lifetime'],
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => isset($_SERVER['HTTPS']),
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            }
        }
    }

    public static function regenerate()
    {
        session_regenerate_id(true);
    }

    public static function destroy()
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }
}
?>
