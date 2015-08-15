<?php

Class MarkedPostsAction extends \Local\BaseAction {

    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getMarks()->getPosts()->getTotal();

        if( $this->account ) {
            $this->getMarked();
        }

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function getTotal() {
        $accountDataModel = new AccountDataModel();

        $account_data = $accountDataModel->get( $this->params['account_id'] );

        $this->data['total'] = $account_data['mark'];
        return $this;
    }

    private function getMarked() {
        if( !count( $this->data[ 'posts' ] ) ) {
            return $this;
        }
        $markModel = new MarkModel();

        $account_id = $this->account[ 'id' ];

        foreach( $this->data['posts'] as &$post ) {
            $mark = $markModel->getMarkByPostAndAccount( 
                $post[ 'id' ], 
                $account_id
            );

            $post[ 'mark' ] = $mark ? $mark[ 'id' ] : 0;
        }
        return $this;
    }

    private function getMarks() {
        $params = $this->params;

        $markModel = new MarkModel();

        $marks = $markModel->select( [
            'where' => [
                [ 'account_id', $params['account_id'] ]
            ],
            'order' => [ [ 'id', 'DESC' ] ],
            'rn' => $params[ 'rn' ]
        ] );

        if( $marks === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->pool[ 'marks' ] = $marks;

        return $this;
    }

    private function getPosts() {
        $posts = array();
        $marks = $this->pool[ 'marks' ];

        if( !count( $marks ) ) {
            $this->data[ 'posts' ] = $posts;
            return $this;
        }

        $postDataModel = new PostDataModel();
        $postModel = new PostModel();   
        $topicModel = new TopicModel();
        $accountModel = $this->accountModel ? $this->accountModel : new AccountModel();

        foreach( $marks as $mark ) {
            $post_id = $mark['post_id'];

            $post_data = $postDataModel->get( $post_id );

            if( $post_data['status'] != 0 ) {
                $posts[] = [
                    'id' => $post_id,
                    'deleted' => 1,
                    'status' => $post_data['status']
                ];

                continue;
            }

            $post = $postModel->get( $post_id );
            $post['data'] = $post_data;
            $post['account'] = $accountModel->get( $post[ 'account_id' ], array( 'id', 'uname', 'desc' ) );
            $post['topic'] = $topicModel->get( $post['topic_id'] );
            $posts[] = $post;
        }

        $this->data['posts'] = $posts;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $account_id = $request->getQuery( 'account_id' );

        if( is_null( $account_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $cursor = intval( $request->getQuery( 'cursor' ) );

        if( $cursor < 0 ) $cursor = 0;

        $rn = intval( $request->getQuery( 'rn' ) );

        if( $rn <= 0 ) $rn = 20;
        if( $rn > 100 ) $rn = 100;

        $this->params = array(
            'account_id' => $account_id,
            'cursor' => $cursor,
            'rn' => $rn
        );

        return $this;
    }
}
