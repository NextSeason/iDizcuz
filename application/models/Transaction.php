<?php

Class TransactionModel extends BaseModel {

    public function vote( $data, $postData = null ) {
        try {
            $this->db->beginTransaction();

            $vote_id = $this->_insert( $data, 'votes' );

            if( !$vote_id ) {
                throw new PDOException( 'failed to insert data into table votes' );
            }

            if( !$postData ) {
                $postData = $this->_get( $data[ 'post_id' ], 'posts_data' );
                if( !$postData ) {
                    throw new PDOException( 'failed to get data from table posts_data' );
                }
            }

            $opinion = $data[ 'opinion' ] == 1 ? 'agree' : 'disagree';

            $query = sprintf( 'UPDATE `posts_data` SET `%s`=`%s`+:value WHERE `id`=:id', $opinion, $opinion );

            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':value', $data[ 'value' ] );
            $stmt->bindValue( ':id', intval( $data[ 'post_id' ] ), PDO::PARAM_INT );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to update data in posts_data' );
            }


            if( $postData[ 'point_id' ] != 0 ) {
                // update points_data 
            }

            $query = sprintf( 'UPDATE `topics_data` SET `%s`=`%s`+:value WHERE `id`=:id', $opinion, $opinion );

            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':value', $data[ 'value' ] );
            $stmt->bindValue( ':id', intval( $postData[ 'topic_id' ] ), PDO::PARAM_INT );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to update data in topics_data' );
            }

            return $this->db->commit();

        } catch( PDOException $e ) {
            var_dump( $e->getMessage() );
            $this->db->rollback();
            return false;
        }
    }

    /*
    public function updateVote( $vote, $opinion, $value, $postData = null ) {
        $old_value = $vote[ 'value' ];
        $old_opinion = $vote[ 'opinion' ];
        $diff = 0;

        try {
            $this->db->beginTransaction();

            $query = 'UPDATE `votes` SET `opinion`=:opinion, `value`=:value WHERE `id`=:id';

            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':id', $vote[ 'id' ] );
            $stmt->bindValue( ':opinion', $opinion );
            $stmt->bindValue( ':value', $value );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to update vote data' );
            }

            if( is_null( $post ) ) {
                $postData = $this->_get( $vote[ 'post_id' ], 'posts_data' );
            }



            return $this->db->commit();

        } cache ( PDOException $e ) {
            $this->db->roolback();
            return false;
        }
    }
     */
    public function addPost( $data ) {
        /**
         * insert new post into table posts
         */
        try {
            $this->db->beginTransaction();

            /**
             * insert new post into database
             */
            $post_id = $this->_insert( $data, 'posts' );

            $topic_id = $data[ 'topic_id' ];

            $post_data = [
                'id' => $post_id,
                'topic_id' => $topic_id
            ];

            if( isset( $data[ 'point_id' ] ) ) {
                $post_data[ 'point_id' ] = $data[ 'point_id' ];
            }


            $this->_insert( $post_data, 'posts_data' );

            /**
             * update post_cnt in table topics_data
             */
            $this->increment( $topic_id, [ 'post_cnt' => 1 ], 'topics_data' );

            if( isset( $data[ 'point_id' ] ) ) {
                $this->increment( $data[ 'point_id' ], [ 'post_cnt' => 1 ], 'points_data'  );
            }

            $this->db->commit();

            return $post_id;

        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function addTopic( $data ) {

        try {
            $this->db->beginTransaction();

            $topic_id = $this->_insert( $data, 'topics' );

            if( !$topic_id ) {
                throw new PDOException( 'failed to insert data into topics' );
            }

            /**
             * insert new data into table topics_data
             */
            $this->_insert( array( 'id' => $topic_id ), 'topics_data' );

            $this->db->commit();

            return $topic_id;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function addAccount( $data ) {

        try {
            $this->db->beginTransaction();

            $account_id = $this->_insert( $data, 'accounts' );

            $data = array(
                'id' => $account_id
            );

            $this->_insert( $data, 'accounts_data' ); 

            $this->_insert( $data, 'accounts_info' ); 

            $this->db->commit();

            return $account_id;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }
}
