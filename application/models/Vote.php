<?php

Class VoteModel extends BaseModel {
    protected $table = 'votes';

    public function getVoteByPostAndAccount( $post, $account, $opinion, $type = 0 ) {
        $query = 'SELECT * FROM `votes` WHERE `post_id`=:post AND `account_id`=:account AND `opinion`=:opinion AND `type`=:type';

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':post', $post );
            $stmt->bindValue( ':account', $account );
            $stmt->bindValue( ':opinion', $opinion );
            $stmt->bindValue( ':type', $type );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to get data from table votes' );
            }

            $vote = $stmt->fetch( PDO::FETCH_ASSOC );

            $this->db->commit();

            return $vote;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function getVotesByAccount( $account, $opinion, $order = '`id` DESC', $start = 0, $len = 20 ) {
        $query = sprintf( 'SELECT * FROM `votes` WHERE `account_id`=:account AND `opinion`=:opinion ORDER BY %s LIMIT :start, :len', $order );

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare( $query );

            $stmt->bindValue( ':account', $account );
            $stmt->bindValue( ':opinion', $opinion );
            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':len', (int)$len, PDO::PARAM_INT );

            $stmt->execute();

            $votes = $stmt->fetchAll( PDO::FETCH_ASSOC );

            $this->db->commit();
            return $votes;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }
}
