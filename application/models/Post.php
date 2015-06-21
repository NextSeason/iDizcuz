<?php
/**
 * @author LvChengbin
 */

Class PostModel extends BaseModel {
    protected $table = 'posts';

    public function getPostsByTopic( $topic_id, $order, $start = 0, $len= 20 ) {
        $query = sprintf( 'SELECT * FROM `posts` WHERE `topic_id` = :topic_id ORDER BY %s LIMIT :start, :len', $order);
        try {
            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':topic_id', $topic_id );
            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':len', (int)$len, PDO::PARAM_INT );

            $stmt->execute();

            $posts = $stmt->fetchAll( PDO::FETCH_CLASS );

            return $posts; 
        } catch( PDOException $e ) {
            return false;
        }
    }
}
