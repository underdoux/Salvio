<?php
// Base Model class for Salvio POS system

class Model
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // Common methods for all models can be added here
}
?>
