<?php

Class BaseModel {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function get( $id ) {
        if( empty( $this->table ) ) return false;

        $query = 'SELECT * FROM `' . $this->table . '` WHERE `id` = :id';

        try {
            $stmt = $this->db->prepare( $query );
            $stmt->bindParam( ':id', $id );
            $stmt->execute();

            return $stmt->fetch();

        } catch( PDOException $e ) {
            return false;
        }
    }

    public function insert( $data ) {
        $keys = array();
        $values = array();

        foreach( $data as $k => $v ) {
            $keys[] = '`' . $k . '`';
            $values[] = ':' . $k;
        }

        $keys = implode( ',', $keys );
        $values = implode( ',', $values );

        $query = sprintf( 'INSERT INTO `' . $this->table . '`(%s) VALUES(%s)', $keys, $values );

        $stmt = $this->db->prepare( $query );

        foreach( $data as $key => &$value ) {
            $stmt->bindParam( ':' . $key, $value );
        }

        return !$stmt->execute() ? false : $this->db->lastInsertId();
    }

    public function save() {
    }

    /*
    public function select( $select, $condition, $orderby = '`id` DESC', $start = 0, $length = 1 ) {
        $query = 'SELECT ';

        if( is_array( $select ) ) {
            array_walk( $select, function( &$value ) {
                $value = '`' . $value '`';
            } );

            $select = implode( ',', $select );
        } else if( is_string( $select ) {
            if( $select != '*' ) {
                $select = '`' . $select . '`';
            }
        }

        $query = sprintf( 'SELECT %s FROM `%s` WHERE %s %s limit %d, %d', $this->table, $condition, $orderby, $start, $length );

        try {
            $stmt = $this->db->prepare( $query );
        }

    }
     */
}
