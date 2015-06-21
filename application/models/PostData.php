<?php

Class PostDataModel extends BaseModel {
    protected $table = 'posts_data';

    public function getPostsByTopic( $topic, $order, $start = 0, $len = 20 ){
        $query = sprintf( 'SELECT * FROM `posts_data` WHERE `topic_id` = :topic_id ORDER BY %s LIMIT :start, :len', $order);
        try {
            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':topic_id', $topic );
            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':len', (int)$len, PDO::PARAM_INT );

            $stmt->execute();

            $posts = $stmt->fetchAll( PDO::FETCH_ASSOC );

            return $posts; 
        } catch( PDOException $e ) {
            return false;
        }

    }

    public function getPostsByPoint( $point, $order, $start = 0, $len ) {
    }
}
