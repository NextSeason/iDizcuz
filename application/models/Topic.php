<?php

/**
 * @author LvChengbin
 */

Class TopicModel extends BaseModel {
    protected $table = 'topics';

    public function getCurrentFocus( $type = 0 ) {

        $query = 'SELECT * FROM `topics` WHERE `type` = :type AND `status` = 1 AND  `start` < :start ORDER BY `start` DESC LIMIT 0, 1';

        try {
            $stmt = $this->db->prepare( $query );
            $stmt->bindParam( ':start', Date( 'Y-m-d H:i:s', strtotime( 'tomorrow' ) ) );
            $stmt->bindParam( ':type', $type );
            $stmt->execute();

            return $stmt->fetch();
        } catch( PDOException $e ) {
            return false;
        }
    }

    public function getFocusBeforeDate( $date ) {
    }
}
