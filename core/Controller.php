<?php
// Base Controller class for Salvio POS system

class Controller
{
    protected function view(string $view, array $data = [])
    {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewFile)) {
            extract($data);
            require $viewFile;
        } else {
            http_response_code(500);
            echo "View file not found: " . htmlspecialchars($view);
            exit;
        }
    }

    protected function redirect(string $url)
    {
        header('Location: ' . $url);
        exit;
    }

    protected function jsonResponse(array $data, int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>
