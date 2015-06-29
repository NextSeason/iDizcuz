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

    public function updateVoteOpinion( $id, $opinion, $value = 1 ) {
        $query = 'UPDATE `votes` SET `opinion` = :opinion, `value` = :value';

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':opinion', $opinion );
            $stmt->bindValue( ':value', $value );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to update data in vote' );
            }

            return $this->db->commit();
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }
}
