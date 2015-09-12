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
}
