<?php

Class MessageModel extends BaseModel {
    protected $table = 'messages';

    public function getAccountReceivedSystemMessagesByType( $account, $type = null, $read = null, $start = 0, $len = 20 ) {

        $query = 'SELECT * FROM `messages` WHERE `from`=0 AND `to`=:to';

        if( !is_null( $type ) ) {
            $query .= ' AND `type`=:type ';
        } 
        if( !is_null( $read ) ) {
            $query .= ' AND `read`=:read ';
        }

        $query .= ' LIMIT :start, :len';

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );

            $stmt->bindValue( ':to', $account );
            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':len', (int)$len, PDO::PARAM_INT );

            if( !is_null( $type ) ) {
                $stmt->bindValue( ':type', $type );
            }

            if( !is_null( $read ) ) {
                $stmt->bindValue( ':read', $read );
            }

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to get data from messages' );
            }

            $messages = $stmt->fetchAll( PDO::FETCH_ASSOC );

            $this->db->commit();

            return $messages;

        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }

    }
}
