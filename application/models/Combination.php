<?php

Class CombinationModel extends BaseModel {
    /**
     * the result of getPost not contained post_data in it.
     * because post_data is always used to sort or filter data.
     * so, before get post data with this method, you must get data
     * from table posts_data first
     */
    public function getPost( $params ) {
        $id = $params['id'];

        try {
            $this->db->beginTransaction();

            $post = $this->_get( $id, 'posts' );

            if( !$post ) {
                throw new PDOException( 'failed to get data from table posts' );
            }

            $account = $this->_get( $post['account_id'], 'accounts', [ 'id', 'uname' ] );

            if( !$account ) {
                throw new PDOException( 'failed to get data from table accounts' );
            }

            $topic = $this->_get( $post['topic_id'], 'topics', ['id', 'title'] );

            if( !$topic ) {
                throw new PDOException( 'failed to get data from table topics' );
            }

            $post['account'] = $account;
            $post['topic'] = $topic;

            $this->db->commit();

            return $post;

        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }
}
