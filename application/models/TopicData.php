<?php

Class TopicDataModel extends BaseModel {
    protected $table = 'topics_data';

    public function getTopicsData( $params ) {
        $type = $params['type'];
        $start = $params['start'];
        $rn = $params[ 'rn' ];

        if( is_null( $type ) ) {
            $query = 'SELECT * FROM `topics_data` WHERE `status` = 1 ORDER BY `id` DESC LIMIT :start, :rn';
        } else {
            $query = sprintf( 'SELECT * FROM `topics_data` WHERE `status` = 1 AND `type`=%d ORDER BY `id` DESC LIMIT :start, :rn', $type );
        }

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );

            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':rn', (int)$rn, PDO::PARAM_INT );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to get data from topics' );
            }

            $topics = $stmt->fetchAll( PDO::FETCH_ASSOC );
            $this->db->commit();

            return $topics;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function adminGetTopicsData( $params ) {
        $type = $params['type'];
        $start = $params['start'];
        $rn= $params[ 'rn' ];

        if( is_null( $type ) ) {
            $query = 'SELECT * FROM `topics_data` ORDER BY `id` DESC LIMIT :start, :rn';
        } else {
            $query = sprintf( 'SELECT * FROM `topics_data` WHERE `type`=%d ORDER BY `id` DESC LIMIT :start, :rn', $type );
        }

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );

            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':rn', (int)$rn, PDO::PARAM_INT );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to get data from topics' );
            }

            $topics = $stmt->fetchAll( PDO::FETCH_ASSOC );
            $this->db->commit();

            return $topics;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }

    }


}
