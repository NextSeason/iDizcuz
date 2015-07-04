<?php

/**
 * @author LvChengbin
 */

Class TopicModel extends BaseModel {
    protected $table = 'topics';

    public function getCurrentFocus( $type = 0 ) {

        $query = 'SELECT * FROM `topics` WHERE `type` = :type AND `status` = 1 AND  `start` < :start ORDER BY `start` DESC LIMIT 0, 1';

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );
            $stmt->bindParam( ':start', Date( 'Y-m-d H:i:s', strtotime( 'tomorrow' ) ) );
            $stmt->bindParam( ':type', $type );
            $stmt->execute();

            $topic = $stmt->fetch( PDO::FETCH_ASSOC );
            
            $this->db->commit();

            return $topic;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function getFocusBeforeDate( $date ) {
    }

    public function getTopics( $order = '`id` DESC', $start = 0, $len = 20 ) {
        $query = sprintf( 'SELECT * FROM `topics` ORDER BY %s LIMIT :start, :len', $order );

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );

            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':len', (int)$len, PDO::PARAM_INT );

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
