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
            $this->db->beginTransaction();

            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':email', $email );
            $stmt->execute();

            $account = $stmt->fetch();
            $this->db->commit();
            return $account;

        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function updatePasswd( $params ) {
        $email = $params['email'];
        $passwd = $params['passwd'];
        $salt = $params['salt'];

        $query = sprintf( 'UPDATE `accounts` SET `passwd`=:passwd, `salt`=:salt WHERE `email`="%s" LIMIT 1', $email );

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':passwd', $passwd );
            $stmt->bindValue( ':salt', $salt );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to update password' );
            }

            return $this->db->commit();
        } catch( PDOException $e ) {
            $this->db->rollback();
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
