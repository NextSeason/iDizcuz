<?php

/**
 * @author LvChengbin
 * @description AccountModel is a DAO class for table accounts
 */
Class AccountModel extends BaseModel {

    protected $table = 'accounts';

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

    public function get( $id, $filter = null ) {
        $account = parent::get( $id );

        if( !$account ) return false;

        if( is_null( $filter ) ) {
            return $account;
        }

        $result = [];

        foreach( $filter as $k ) {
            $result[ $k ] = $account[ $k ];
        }
        return $result;
    }
}
