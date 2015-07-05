<?php
/**
 * @author LvChengbin
 */

Class PostModel extends BaseModel {
    protected $table = 'posts';

    public function getPostsByTopic( $topic_id, $order, $start = 0, $len= 20 ) {
        $query = sprintf( 'SELECT * FROM `posts` WHERE `topic_id` = :topic_id ORDER BY %s LIMIT :start, :len', $order);
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':topic_id', $topic_id );
            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':len', (int)$len, PDO::PARAM_INT );

            $stmt->execute();

            $posts = $stmt->fetchAll( PDO::FETCH_ASSOC );

            $this->db->commit();
            return $posts; 
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function getPostsByAccount( $account, $order, $start = 0, $len = 20 ) {
        $query = sprintf( 'SELECT * FROM `posts` WHERE `account_id` = :account_id ORDER BY %s LIMIT :start, :len', $order);

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':account_id', $account );
            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':len', (int)$len, PDO::PARAM_INT );

            $stmt->execute();

            $posts = $stmt->fetchAll( PDO::FETCH_ASSOC );

            $this->db->commit();

            return $posts;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }
}
