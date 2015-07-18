<?php

Class FollowModel extends BaseModel {
    protected $table = 'follows';

    public function getFansByAccount( $params ) {
        $account_id = $params['account_id'];
        $columns = $params['columns'];
        $start = $params['start'];
        $rn = $params['rn'];

        $query = 'SELECT ' . $this->formatColumns( $columns ) . ' FROM `follows` WHERE `account_id`=:account_id ORDER BY `id` DESC LIMIT :start, :rn';

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':account_id', $account_id );
            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':rn', (int)$rn, PDO::PARAM_INT );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to get data frow follow' );
            }

            $follows = $stmt->fetchAll( PDO::FETCH_ASSOC );

            $this->db->commit();

            return $follows;

        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }

    }

    public function getFollowsByAccount( $params ) {
        $fans_id = $params[ 'fans_id' ];
        $columns = $params[ 'columns' ];
        $start = $params['start'];
        $rn = $params['rn'];

        $query = 'SELECT ' . $this->formatColumns( $columns ) . ' FROM `follows` WHERE `fans_id`=:fans_id ORDER BY `id` DESC LIMIT :start, :rn';

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':fans_id', $fans_id );
            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':rn', (int)$rn, PDO::PARAM_INT );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to get data frow follow' );
            }

            $follows = $stmt->fetchAll( PDO::FETCH_ASSOC );

            $this->db->commit();

            return $follows;

        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function getFollowStatus( $params ) {
        $account_id = $params['account_id'];
        $fans_id = $params['fans_id'];

        $query = 'SELECT `id` FROM `follows` WHERE `account_id`=:account_id AND `fans_id`=:fans_id LIMIT 1';

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':account_id', $account_id );
            $stmt->bindValue( ':fans_id', $fans_id );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to get data from follow' );
            }
            
            $follow = $stmt->fetch();

            $this->db->commit();
            return $follow;

        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }
}
