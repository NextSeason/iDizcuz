<?php

Class MarkModel extends BaseModel {
    protected $table = 'marks';

    public function removeMarkByPostAndAccount( $post, $account ) {
        $query = 'DELETE FROM `marks` WHERE `post_id` = :post AND `account_id` = :account';

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':post', $post );
            $stmt->bindValue( ':account', $account );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to delete mark from marks' );
            }

            return $this->db->commit();


        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function getMarkByPostAndAccount( $post, $account ) {
        $query = 'SELECT * FROM `marks` WHERE `post_id` = :post AND `account_id` = :account';

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':post', $post );
            $stmt->bindValue( ':account', $account );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to get mark from table marks' );
            }

            $this->db->commit();

            return $stmt->fetch( PDO::FETCH_ASSOC );

        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function getMarksByAccount( $account, $order, $start, $len ) {
        $query = sprintf( 'SELECT * FROM `marks` WHERE `account_id`=:account ORDER BY %s LIMIT :start, :len', $order );

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );

            $stmt->bindValue( ':account', $account );
            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':len', (int)$len, PDO::PARAM_INT );

            $stmt->execute();

            $marks = $stmt->fetchAll( PDO::FETCH_ASSOC );
            $this->db->commit();
            return $marks;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }
}
