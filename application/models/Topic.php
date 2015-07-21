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
            $stmt->bindValue( ':start', Date( 'Y-m-d H:i:s', strtotime( 'tomorrow' ) ) );
            $stmt->bindValue( ':type', $type );
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
}
