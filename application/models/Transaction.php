<?php

Class TransactionModel extends BaseModel {

    public function readMessage( $account, $id ) {
        try {
            $this->db->beginTransaction();

            $query = 'UPDATE `messages` SET `read` = 1 WHERE `id` = :id AND `to` = :to';

            $stmt = $this->db->prepare( $query );

            $stmt->bindValue( ':id', $id );
            $stmt->bindValue( ':to', $account );

            $res = $stmt->execute();

            if( !$res ) {
                throw new PDOException( 'failed to update data in table messages' );
            }

            $this->increment( $account, [ 'unread_msg' => -1 ], 'accounts_data' );

            $this->db->commit();

            return $res;

        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function sendMessage( $data ) {
        try {
            $this->db->beginTransaction();

            $message = $this->_insert( $data, 'messages' );

            if( !$message ) {
                throw new PDOException( 'failed to insert data into table messages' );
            }

            $this->increment( $data[ 'to' ], [ 
                'unread_msg' => 1,
                'msg_cnt' => 1
            ], 'accounts_data' );

            $this->db->commit();
        } catch( PDOException $e ) {
            $this->rollback();
            return false;
        }
    }

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

            $value = $data[ 'value' ];

            $this->increment( $data[ 'post_id' ], [ $opinion => $value ], 'posts_data' );

            $this->increment( $postData[ 'topic_id' ], [ $opinion => $value ], 'topics_data' );

            if( $postData[ 'point_id' ] != 0 ) {
                $this->increment( $postData[ 'point_id' ], [ $opinion => $value ], 'points_data' );
            }

            return $this->db->commit();

        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

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

            if( isset( $data[ 'to' ] ) && $data['to'] != 0 ) {
                $this->increment( $data['to'], [ 'to_times' ], 'posts_data' );
            }

            $this->increment( $data[ 'account_id' ], [ 'post_cnt' => 1 ], 'accounts_data' );

            $this->db->commit();

            return $post_id;

        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function addComment( $data ) {
        try {
            $this->db->beginTransaction();

            $comment_id = $this->_insert( $data, 'comments' );

            if( !$comment_id ) {
                throw new PDOException( 'failed to insert data into comments' );
            }

            $this->increment( $data[ 'post_id' ], [ 'comments_cnt' => 1 ], 'posts_data' );

            $this->db->commit();

            return $comment_id;

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
