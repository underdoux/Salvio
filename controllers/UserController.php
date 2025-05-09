<?php
require_once __DIR__ . '/../core/Controller.php';

class UserController extends Controller
{
    public function index()
    {
        // Placeholder for users management page
        $this->view('users/index', ['title' => 'Users']);
    }
}
?>
