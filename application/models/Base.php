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

    public function insert( $data, $table = null ) {

        if( is_null( $table ) ) $table = $this->table;

        $keys = array();
        $values = array();

        foreach( $data as $k => $v ) {
            $keys[] = '`' . $k . '`';
            $values[] = ':' . $k;
        }

        $keys = implode( ',', $keys );
        $values = implode( ',', $values );

        $query = sprintf( 'INSERT INTO `' . $table . '`(%s) VALUES(%s)', $keys, $values );

        try {

            $stmt = $this->db->prepare( $query );

            foreach( $data as $k => &$v ) {
                $stmt->bindParam( ':' . $k, $v );
            }
                
            $stmt->execute();

            return $this->db->lastInsertId();

        } catch( PDOException $e ) {
            return false;
        }
    }

    public function increment( $id, $column, $table = null ) {
        if( is_null( $table ) ) $table = $this->table;

        $query = 'UPDATE `' . $table . '` SET ';

        $update = array();

        foreach( $column as $k => $v ) {
            $update[] = sprintf( '`%s`=`%s`+%d', $k, $k, (int)$v );
        }

        $query .= implode( ',', $update ) . ' WHERE `id` = :id';

        try {
            $stmt = $this->db->prepare( $query );
            $stmt->bindParam( ':id', $id );

            return $stmt->execute();
        } catch( PDOException $e ) {
            return false;
        }
        
    } 

    public function save() {
    }
}
