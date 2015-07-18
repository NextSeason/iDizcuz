<?php

Class UserPostsAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getPostsData()->getPosts()->getTopics()->getAccount()->getTotal();

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

    private function getTotal() {
        $accountDataModel = new AccountDataModel();
        $account_data = $accountDataModel->get( $this->pool['account']['id'] );
        $this->data['total'] = $account_data['post_cnt'];
    }

    private function getAccount() {
        if( $this->account && $this->params['account'] == $this->account['id'] ) {
            $account = $this->account;
        } else {
            $accountModel = $this->accountModel ? $this->accountModel : new AccountModel();
            $account = $accountModel->get( $this->params[ 'account' ], array( 'id', 'uname', 'desc' ) );
        }

        $this->pool['account'] = $account;

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
        $postDataModel = new PostDataModel();

        $params = $this->params;

        $posts_data = $postDataModel->getPostsByAccount( [
            'account' => $params[ 'account' ],
            'start' => $params['start'],
            'rn' => $params['rn'],
            'columns' => null
        ] );

        if( $posts_data === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->pool[ 'posts_data' ] = $posts_data;

        return $this;
    }

    private function getPosts() {
        $posts_data = $this->pool['posts_data'];

        if( !count( $posts_data ) ) {
            $this->data['posts'] = [];
            return $this;
        }

        $postModel = new PostModel();

        $posts = [];

        foreach( $posts_data as $post_data ) {
            $post = $postModel->get( $post_data['id'] );
            $post['data'] = $post_data;
            $posts[] = $post;
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
