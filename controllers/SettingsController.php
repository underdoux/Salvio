<?php
require_once __DIR__ . '/../core/Controller.php';

class SettingsController extends Controller
{
    public function index()
    {
        // Placeholder for settings page
        $this->view('settings/index', ['title' => 'Settings']);
    }
}
?>
