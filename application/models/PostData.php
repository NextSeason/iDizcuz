<?php

Class PostDataModel extends BaseModel {
    protected $table = 'posts_data';

    public function getPostsByTopic( $topic, $order, $start = 0, $len = 20 ){
        $query = sprintf( 'SELECT * FROM `posts_data` WHERE `topic_id` = :topic_id AND `status`=0 ORDER BY %s LIMIT :start, :len', $order);
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':topic_id', $topic );
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

    public function getRemovedPostsByAccount( $account, $status = null, $start = 0, $len = 20 ) {
        $query = 'SELECT * FROM `posts_data` WHERE `account_id`=:account ';

        if( !is_null( $status ) ) {
            $query .= ' AND `status`=:status ';
        } else {
            $query .= ' AND `status`>0 ';
        }
        $query .= ' ORDER BY `id` DESC LIMIT :start, :len';

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':account', $account );
            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':len', (int)$len, PDO::PARAM_INT );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to get data from posts_data' );
            }

            $posts = $stmt->fetchAll( PDO::FETCH_ASSOC );

            $this->db->commit();

            return $posts;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function getPostsByPoint( $point, $order, $start = 0, $len ) {
    }
}
