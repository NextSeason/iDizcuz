<?php

Class CommentModel extends BaseModel {
    protected $table = 'comments';

    public function getCommentsByPost( $post_id, $start, $len = 20 ) {
        $query = 'SELECT * FROM `comments` WHERE `post_id`=:post_id LIMIT :start, :len';

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );

            $stmt->bindValue( ':post_id', $post_id );
            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':len', (int)$len, PDO::PARAM_INT );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to get data from comments' );
            }

            $comments = $stmt->fetchAll( PDO::FETCH_ASSOC );

            $this->db->commit();

            return $comments;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }
}
