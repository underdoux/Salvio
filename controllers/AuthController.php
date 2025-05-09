<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller
{
    public function login()
    {
        Session::start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $this->view('auth/login', ['error' => 'Please enter username and password.']);
                return;
            }

            $userModel = new User();
            $user = $userModel->findByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                Auth::login($user);
                // Redirect based on role
                switch ($user['role']) {
                    case 'Admin':
                        $this->redirect('/admin/dashboard');
                        break;
                    case 'Cashier':
                        $this->redirect('/cashier/dashboard');
                        break;
                    case 'Sales':
                        $this->redirect('/sales/dashboard');
                        break;
                    default:
                        $this->redirect('/');
                        break;
                }
            } else {
                $this->view('auth/login', ['error' => 'Invalid username or password.']);
            }
        } else {
            $this->view('auth/login');
        }
    }

    public function logout()
    {
        Session::start();
        Auth::logout();
        $this->redirect('/login');
    }
}
?>
