<?php

Class BaseModel {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function insert() {
    }

    public function save() {
    }
}
