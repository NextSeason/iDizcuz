<?php

Class UserPostsAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getPosts()->getPostsData()->getTopics()->getAccount();

        if( $this->account ) {
            $this->getMarked();
        }

        return $this->data;
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

    private function getAccount() {
        $accountModel = $this->accountModel ? $this->accountModel : new AccountModel();

        $account = $accountModel->get( $this->params[ 'account' ], array( 'id', 'uname', 'desc' ) );

        foreach( $this->data[ 'posts'] as &$post ) {
            $post[ 'account' ] = $account;
        }

        return $this;
    }

    private function getTopics() {
        if( count( $this->data[ 'posts' ] ) == 0 ) {
            return $this;
        }

        $topicModel = new TopicModel();

        foreach( $this->data[ 'posts' ] as &$post ) {
            $post[ 'topic' ] = $topicModel->get( $post['topic_id'] );
        }

        return $this;
    }

    private function getPostsData() {
        if( count( $this->data[ 'posts' ] ) == 0 ) {
            return $this;
        }

        $postDataModel = new PostDataModel();

        foreach( $this->data[ 'posts' ] as &$post ) {
            $post[ 'data' ] = $postDataModel->get( $post['id'] );
        }

        return $this;
    }

    private function getPosts() {
        $postModel = new PostModel();

        $posts = $postModel->getPostsByAccount( $this->params[ 'account' ], '`id` DESC' );

        if( !$posts ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data[ 'posts'] = $posts;
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
