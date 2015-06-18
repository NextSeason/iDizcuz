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
}
