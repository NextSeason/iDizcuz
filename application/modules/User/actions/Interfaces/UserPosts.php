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

    public function __mobile() {
        return $this->__execute();
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
        if( $this->account && $this->params['account_id'] == $this->account['id'] ) {
            $account = $this->account;
        } else {
            $accountModel = $this->accountModel ? $this->accountModel : new AccountModel();
            $account = $accountModel->get( $this->params[ 'account_id' ], array( 'id', 'uname', 'desc' ) );
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

        $where = [
            [ 'account_id', $params['account_id'] ],
            [ 'status', 0 ]
        ];

        if( $params['cursor'] != 0 ) {
            $where[] = [ 'id', '<', $params[ 'cursor' ] ];
        }

        $posts_data = $postDataModel->select( [
            'where' => $where,
            'order' => [ [ 'id', 'DESC' ] ],
            'rn' => $params['rn']
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
            if( $post['to'] != 0 ) {
                $post['to'] = $postModel->get( $post['to'], [ 'id', 'title' ] );
            }

            if( $this->account && $post['account_id'] == $this->account['id'] ) {
                $post['own'] = 1;
            } else {
                $post['own'] = 0;
            }
            $posts[] = $post;
        }

        $this->data[ 'posts'] = $posts;

        return $this;
    }

    private function paramsProcessing() {
        $account_id = $this->__getQuery( 'account_id' );

        if( is_null( $account_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $cursor = intval( $this->__getQuery( 'cursor' ) );

        if( $cursor < 0 ) $cursor = 0;

        $rn = $this->__getQuery( 'rn' );

        $rn = intval( $rn );

        if( $rn == 0 ) $rn = 20;

        if( $rn > 100 ) $rn = 100;

        $this->params = array(
            'account_id' => $account_id,
            'cursor' => $cursor,
            'rn' => $rn
        );

        return $this;
    }
}
