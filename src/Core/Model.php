<?php
namespace App\Core;

abstract class Model {
    protected \mysqli $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }
}
