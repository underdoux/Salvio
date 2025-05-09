<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';

class DashboardController extends Controller
{
    public function index()
    {
        // Require user to be logged in
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $user = Auth::user();

        // Load dashboard view with user data
        $this->view('dashboard/index', ['user' => $user]);
    }
}
?>
