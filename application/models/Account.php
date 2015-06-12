<?php

/**
 * @author LvChengbin
 * @description AccountModel is a DAO class for table accounts
 */
Class AccountModel extends BaseModel {

    public function getAccountByEmail( $email ) {
        $query = 'SELECT * FROM `accounts` WHERE `email` = :email';

        try {
            $stmt = $this->db->prepare( $query );
            $stmt->bindParam( ':email', $email );
            $stmt->execute();

            return $stmt->fetch();

        } catch( PDOException $e ) {
            return false;
        }
    }

    public function addAccount( $data ) {
        $query = 'INSERT INTO `accounts`( `email`, `passwd`, `salt`, `uname`, `reg_ip`, `login_ip` ) VALUES( :email, :passwd, :salt, :uname, :reg_ip, :login_ip )';

        $stmt = $this->db->prepare( $query );

        foreach( $data as $key => &$value ) {
            $stmt->bindParam( ':' . $key, $value );
        }
        if( !$stmt->execute() ) {
            return false;
        }
        return $this->db->lastInsertId();
    }
}
