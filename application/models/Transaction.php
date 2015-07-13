<?php

Class TransactionModel extends BaseModel {

    public function trashPost( $account, $id ) {
        try {
            $this->db->beginTransaction();

            // step 1 get post data to check permission and get point_id , topic_id of this post
            $post_data = $this->_get( $id, 'posts_data' );

            if( !$post_data ) {
                throw new PDOException( 'failed to get data from table posts_data' );
            }

            if( $post_data[ 'account_id' ] != $account ) {
                throw new PDOException( 'user has no permission to remove this post' );
            }

            if( $post_data[ 'status' ] != 0 ) {
                throw new PDOException( 'post has already removed' );
            }

            // step 2 update post_cnt in points_data if point_id is not 0
            if( $post_data[ 'point_id' ] != 0 ) {
                $this->increment( $post_data[ 'point_id' ], [ 'post_cnt' => -1 ], 'points_data' );
            }

            // step 3 update post_cnt in topics_data
            $this->increment( $post_data[ 'topic_id' ], [ 'post_cnt' => -1 ], 'topics_data' );

            // step 4 update post_cnt in accounts_data
            $this->increment( $post_data[ 'account_id' ], [ 'post_cnt' => -1 ], 'accounts_data' );

            // step 5 update score in accounts_data
            // step 5 update status in posts_data
            $this->_update( $id, [ 'status' => 1 ], 'posts_data' );

            return $this->db->commit();

        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function removeReceivedMessage( $account, $id ) {
        try {
            $this->db->beginTransaction();
            // step 1 get message data from messages
            $message = $this->_get( $id, 'messages' );

            if( !$message ) {
                throw new PDOException( 'cannot get data from table messages' );
            }

            if( $message[ 'to' ] != $account ) {
                throw new PDOException( 'user has no permission to remove this message ' );
            }

            // step 2 remove message from tables messages
            $del = $message[ 'del' ];

            // del >= 2 means receiver removed this message or both sender and receiver removed this message
            if( $del >= 2 ) {
                throw new PDOException( 'message has already deleted by receiver' );
            }

            $del = $del == 0 ? 2 : 3;
            $this->_update( $id, [ 'del' => $del ], 'messages' );

            // step 3 if message has not been read, minus value of unread_msg in table accounts_data 
            // step 4 minus value of msg_cnt in table accounts_data
            $data = [
                'msg_cnt' => -1
            ];

            if( $message['read'] == 0 ) {
                $data[ 'unread_msg' ] = -1;
            }

            $this->increment( $account, [ 'msg_cnt' => -1 ], 'accounts_data' );

            return $this->db->commit();
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }

    public function removePost( $account, $id ) {
    }

    public function removeComment( $account, $id ) {
        try {
            $this->db->beginTransaction();

            $comment = $this->_get( $id, 'comments' );

            if( !$comment ) {
                throw new PDOException( 'failed to get data from table comments' );
            }

            if( $comment[ 'account_id' ] != $account ) {
                throw new PDOException( 'has no permission to delete this comment' );
            }

            $post_id = $comment[ 'post_id' ];

            $res = $this->increment( $post_id, [ 'comments_cnt' => -1 ], 'posts_data' );

            if( !$res ) {
                throw new PDOException( 'failed to update data in table posts_data' );
            }

            $res = $this->_remove( $id, 'comments' );

            if( !$res ) {
                throw new PDOException( 'failed to delete data in comments' );
            }

            return $this->db->commit();

        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }


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
        //try {
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
        //} catch( PDOException $e ) {
            //$this->db->rollback();
            //return false;
        //}
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
                'topic_id' => $topic_id,
                'account_id' => $data[ 'account_id' ]
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
