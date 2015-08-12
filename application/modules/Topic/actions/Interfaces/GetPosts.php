<?php

Class GetPostsAction extends \Local\BaseAction {

    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing();

        $this->data[ 'posts' ] = $this->getPosts();
            
        $this->getTotal();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function getTotal() {
        $params = $this->params;

        if( !is_null( $params['point_id'] ) && $params['point_id'] != 0 ) {
            $pointDataModel = new PointDataModel();

            $point_data = $pointDataModel->get( $params['point_id'] );

            if( !$point_data ) {
                $this->error( 'SYSTEM_ERR' );
            }

            $this->data['total'] = $point_data['post_cnt'];
            return $this;
        }

        $topicDataModel = new TopicDataModel();

        $topic_data = $topicDataModel->get( $params['topic_id'] );

        if( !$topic_data ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data['total'] = $topic_data['post_cnt'];

        return $this;
    }

    private function getPosts() {
        $params = $this->params;

        $postDataModel = new PostDataModel();

        if( !is_null( $params[ 'point_id' ] ) && $params['point_id'] != 0 ) {
            $postsData = $postDataModel->getPostsByPoint( [
                'point_id' => $params[ 'point_id' ], 
                'order' => $params[ 'order' ], 
                'start' => $params[ 'start' ], 
                'rn' => $params[ 'rn' ],
                'columns' => [ 'id' ]
            ] );
        } else {
            $postsData = $postDataModel->getPostsByTopic( [
                'topic_id' => $params[ 'topic_id' ],
                'order' => $params[ 'order' ],
                'start' => $params[ 'start' ],
                'rn' => $params[ 'rn' ],
                'columns' => ['id']
            ] );
        }

        if( $postsData === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        foreach( $postsData as &$postData ) {
            $postData = $postData['id'];
        }

        return $postsData;

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
                $post[ 'isMine' ] = $this->account['id'] == $post['account_id'] ? 1 : 0;
            }
        }

        foreach( $posts as &$post ) {
            if( $post['to'] != 0 ) {
                $to = $postModel->get( $post['to'] );
                $post['to'] = array(
                    'id' => $to['id'],
                    'title' => $to['title']
                );
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

        $start = intval( $request->getQuery( 'start' ) );

        if( $start < 0 ) $start = 0;

        $rn = intval( $request->getQuery( 'rn' ) );

        if( $rn == 0 ) $rn = 20;
        if( $rn > 100 ) $rn = 100;

        $this->params = [
            'topic_id' => $topic_id,
            'point_id' => $point_id,
            'order' => $order,
            'start' => $start,
            'rn' => $rn
        ];

        return $this;
    }
}
