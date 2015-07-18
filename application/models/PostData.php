<?php

Class PostDataModel extends BaseModel {
    protected $table = 'posts_data';

    public function getPostsByTopic( $params ){

        $columns = $params[ 'columns' ];
        $topic_id = $params[ 'topic_id' ];
        $order = $params['order'];
        $start = $params['start'];
        $rn = $params['rn'];


        $query = sprintf( 'SELECT ' . $this->formatColumns( $columns ) . ' FROM `posts_data` WHERE `topic_id` = :topic_id AND `status`=0 ORDER BY %s LIMIT :start, :rn', $order);
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':topic_id', $topic_id );
            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':rn', (int)$rn, PDO::PARAM_INT );

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

    public function getPostsByAccount( $params ) {
        $account = $params[ 'account' ];
        $start = $params[ 'start' ];
        $rn = $params[ 'rn' ];
        $columns = $params['columns'];

        $query = 'SELECT ' . $this->formatColumns( $columns ) . ' FROM `posts_data` WHERE `account_id`=:account_id AND `status`=0 ORDER BY `id` DESC LIMIT :start, :rn';

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':account_id', $account );
            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':rn', (int)$rn, PDO::PARAM_INT );

            if( !$stmt->execute() ) {
                throw new PDOException( 'cannot to get data from posts_data' );
            }

            $posts_data = $stmt->fetchAll( PDO::FETCH_ASSOC );

            $this->db->commit();

            return $posts_data;

        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function getPostsByPoint( $point, $order, $start = 0, $len ) {
    }
}
