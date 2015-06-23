<?php

Class GetPostsAction extends \Local\BaseAction {

    private $data = [];

    private $postDataModel;

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing();

        $this->postDataModel = new PostDataModel();

        $this->data[ 'posts' ] = $this->getPosts();

        return $this->data;
    }

    private function getPosts() {
        $params = $this->params;

        if( !is_null( $params[ 'point_id' ] ) ) {
            $postsData = $this->postDataModel->getPostsByPoint( 
                $params[ 'point_id' ], 
                $params[ 'order' ], 
                $params[ 'start' ], 
                $params[ 'len' ] 
            );
        } else {
            $postsData = $this->postDataModel->getPostsByTopic( 
                $params[ 'topic_id' ],
                $params[ 'order' ],
                $params[ 'start' ],
                $params[ 'len' ] 
            );
        }

        if( $postsData === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        if( count( $postsData ) == 0 ) {
            return [];
        }

        $posts = [];

        $postModel = new PostModel();

        /**
         * get post and mark by postsdata
         */
        foreach( $postsData as $postData ) {
            $post = $postModel->get( $postData[ 'id' ] );
            $post[ 'data' ] = $postData;
            $post[ 'mark' ] = 0;
            $posts[] = $post;
        }

        if( !is_null( $this->account ) ) {
            $markModel = new MarkModel();

            $account_id = $this->account[ 'id' ];

            foreach( $posts as &$post ) {
                $mark = $markModel->getMarkByPostAndAccount(
                    $post[ 'id' ],
                    $account_id
                );

                $post[ 'mark' ] = $mark ? $mark[ 'id' ] : 0;
            }
        }

        /**
         * get account info for each post;
         * @todo it is better to add cache for some accounts to reduce times to seach in mysql
         */
        $accountModel = new AccountModel();

        foreach( $posts as &$post ) {
            $account = $accountModel->get( $post[ 'account_id' ], [ 'id', 'uname', 'desc' ] );
            if( !$account) {
                $this->error( 'SYSTEM_ERR' );
            }
            $post[ 'account' ] = $account;
        }

        return $posts;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $topic_id = $request->getQuery( 'topic' );

        if( is_null( $topic_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $point_id = $request->getQuery( 'point' );

        $order = $request->getQuery( 'order' );

        $orderList = [ '`id` DESC', '`agree` DESC', '`disagree` DESC' ];

        $order = is_null( $order ) ? '`id` DESC' : $orderList[ $order ];

        $start = $request->getQuery( 's' );

        if( is_null( $start ) ) $start = 0;

        $len = $request->getQuery( 'l' );

        if( is_null( $len ) ) $len = 20;

        if( $len > 100 ) $len = 100;

        $this->params = [
            'topic_id' => $topic_id,
            'point_id' => $point_id,
            'order' => $order,
            'start' => $start,
            'len' => $len
        ];

        return $this;
    }
}
