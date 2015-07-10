<?php

Class MarkedPostsAction extends \Local\BaseAction {

    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getMarks()->getPosts();

        return $this->data;
    }

    private function getMarks() {
        $params = $this->params;

        $markModel = new MarkModel();

        $marks = $markModel->getMarksByAccount(
            $params[ 'account' ],
            '`id` DESC',
            $params[ 'start' ],
            $params[ 'rn' ]
        );

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

        $postModel = new PostModel();   
        $postDataModel = new PostDataModel();
        $accountModel = $this->accountModel ? $this->accountModel : new AccountModel();

        foreach( $marks as $mark ) {
            $post = $postModel->get( $mark['post_id'] );
            $post['data'] = $postDataModel->get( $mark['post_id'] );
            $post['account'] = $accountModel->get( $post[ 'account_id' ], array( 'id', 'uname', 'desc' ) );
            $post['mark'] = $mark;
            $posts[] = $post;
        }

        $this->data['posts'] = $posts;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $account = $request->getQuery( 'account' );

        if( is_null( $account ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $start = $request->getQuery( 'start' );

        $start = intval( $start );

        $rn = $request->getQuery( 'rn' );

        $rn = intval( $rn );

        if( $rn == 0 ) $rn = 20;
        if( $rn > 100 ) $rn = 100;

        $this->params = array(
            'account' => $account,
            'start' => $start,
            'rn' => $rn
        );

        return $this;
    }
}
