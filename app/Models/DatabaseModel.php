<?php

namespace app\Models;

use app\{
    Models\Model,
    Core\Database
};


class DatabaseModel extends Model {

    private Database $database;
    protected \PDO $pdo;

    public function __construct() {

        $this->database = new Database();
        $this->pdo = $this->database->getPdo();
    }
}
