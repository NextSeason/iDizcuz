<?php

Class ActivityModel extends BaseModel {
    protected $table = 'activities';

    public function getActivitiesByAccount( $params ) {
        $cursor = intval( $params[ 'cursor' ] );
        $account_id = $params['account_id'];
        $rn = $params[ 'rn' ];

        if( $cursor <= 0 ) {
            $query = 'SELECT * FROM `activities` WHERE `account_id`=:account_id ORDER BY `id` DESC  LIMIT 0, :rn';
        } else {
            $query = 'SELECT * FROM `activities` WHERE `id`<:cursor AND `account_id`=:account_id ORDER BY `id` DESC  LIMIT :rn';
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare( $query );

            if( $cursor > 0 ) {
                $stmt->bindValue( ':cursor', $cursor );
            }
            $stmt->bindValue( ':account_id', $account_id );
            $stmt->bindValue( ':rn', (int)$rn, PDO::PARAM_INT ); 

            if( !$stmt->execute() ) {
                throw new PDOException( 'cannot find data from activities' );
            }
            $activities = $stmt->fetchAll( PDO::FETCH_ASSOC );
            $this->db->commit();

            return $activities;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return $this;
        }
    }

    public function getActivities( $params ) {
        $cursor = intval( $params['cursor'] );
        $rn = $params['rn'];

        if( $cursor <= 0 ) {
            $query = 'SELECT * FROM `activities` ORDER BY `id` DESC LIMIT :rn';
        } else {
            $query = 'SELECT * FROM `activities` WHERE `id`<:cursor ORDER BY `id` DESC LIMIT :rn';
        }

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );
            if( $cursor > 0 ) {
                $stmt->bindValue( ':cursor', $cursor );
            }
            $stmt->bindValue( ':rn', (int)$rn, PDO::PARAM_INT );

            if( !$stmt->execute() ) {
                throw new PDOException( 'cannot get data from table activities' );
            }
            $activities = $stmt->fetchAll( PDO::FETCH_ASSOC );
            $this->db->commit();
            return $activities;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return $this;
        }
    }

    public function getFollowingActivities( $params ) {
        $cursor = intval( $params['cursor'] );
        $rn = $params['rn'];
        $follower_id = $params['follower_id'];

        if( $cursor <= 0 ) {
            $query = 'SELECT * FROM `activities` WHERE `account_id` IN ( SELECT `account_id` FROM `follows`  WHERE `fans_id`=:fans_id ) ORDER BY `id` DESC LIMIT :rn';
        } else {
            $query = 'SELECT * FROM `activities` WHERE `id`<:cursor `account_id` IN ( SELECT `account_id` FROM `follows` WHERE `fans_id`=:fans_id ) ORDER BY `id` DESC LIMIT :rn';
        }

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );
            if( $cursor > 0 ) {
                $stmt->bindValue( ':cursor', $cursor );
            }
            $stmt->bindValue( ':fans_id', $follower_id );
            $stmt->bindValue( ':rn', (int)$rn, PDO::PARAM_INT );
            if( !$stmt->execute() ) {
                throw new PDOException( 'cannot get data from table activities' );
            }
            $activities = $stmt->fetchAll( PDO::FETCH_ASSOC );
            $this->db->commit();
            return $activities;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return $this;
        }

    }
}
