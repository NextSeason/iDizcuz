<?php

Class TopicEventModel extends BaseModel {
    protected $table = 'topic_events';

    public function getEvents( $order = '`id` DESC', $start = 0, $len = 40 ) {
        $query = sprintf( 'SELECT * FROM `topic_events` ORDER BY %s LIMIT :start, :len', $order );
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );

            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':len', (int)$len, PDO::PARAM_INT );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to get data from topic_events' );
            }

            $events = $stmt->fetchAll( PDO::FETCH_ASSOC );

            $this->db->commit();
            return $events;
        } catch( PDOException $e ) {
            return false;
        }
    }
}
