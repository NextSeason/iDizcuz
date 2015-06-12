<?php

Class BaseModel {
    protected $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function insert() {
    }

    public function save() {
    }

    public function select( ) {
    }
}
