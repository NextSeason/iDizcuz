<?php

Class BaseModel {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function _get( $id, $table = null ) {
        if( is_null( $table ) ) $table = $this->table;

        $query = 'SELECT * FROM `' . $table . '` WHERE `id` = :id';

        try {
            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':id', $id );
            $stmt->execute();

            return $stmt->fetch( PDO::FETCH_ASSOC );

        } catch( PDOException $e ) {
            return false;
        }
    }

    public function get( $id ) {
        try {
            $this->db->beginTransaction();
            $res = $this->_get( $id );
            if( !$res ) {
                throw new PDOException( 'failed to get data' );
            }
            $this->db->commit();

            return $res;
            
        } catch( PDOException $e ) {
            return false;
        }
    }

    public function gets( $id ) {
        
    }

    public function _insert( $data, $table = null ) {

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

    public function insert( $data ) {
        try {
            $this->db->beginTransaction();
            $res = $this->_insert( $data );
            if( !$res ) {
                throw new PDOException( 'failed to insert data' );
            }
            $this->db->commit();
            return $res;
        } catch( PDOException $e ) {
            print_r( $this->db->errorInfo() );
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

    public function _remove( $id, $table = null ) {
        if( is_null( $table ) ) $table = $this->table;

        $query = 'DELETE FROM `' . $table . '` WHERE `id` = :id';

        try {
            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':id', $id );

            return $stmt->execute();
        } catch( PDOException $e ) {
            return false;
        }
    }

    public function remove( $id ) {
        try {
            $this->db->beginTransaction();
            $res = $this->_remove( $id );
            if( !$res ) throw new PDOException( 'failed to delete data' );
            $this->db->commit();
            return $res;
        } catch( PDOException $e ) {
            return false;
        }
    }
}
