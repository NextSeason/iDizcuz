<?php

Class VotedPostsAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getVotes()->getPosts();

        return $this->data;
    }

    private function getVotes() {
        $params = $this->params;

        $voteModel = new VoteModel();

        $votes = $voteModel->getVotesByAccount( 
            $params[ 'account' ],
            $params[ 'opinion' ],
            '`id` DESC',
            $params[ 'start' ],
            $params[ 'rn' ]
        );

        if( $votes === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->pool[ 'votes' ] = $votes;

        return $this;
    }

    private function getPosts() {
        $posts = array();

        $votes = $this->pool[ 'votes' ];

        if( !count( $votes ) ) {
            $this->data[ 'posts' ] = $posts;
            return $this;
        }

        $postModel = new PostModel();
        $postDataModel = new PostDataModel();
        $accountModel = $this->accountModel ? $this->accountModel : new AccountModel();

        foreach( $votes as $vote ) {
            $post = $postModel->get( $vote[ 'post_id' ] );
            $post[ 'data' ] = $postDataModel->get( $vote[ 'post_id' ] );
            $post[ 'account' ] = $accountModel->get( $post['account_id'], array( 'id', 'uname', 'desc' ) );
            $post[ 'vote' ] = $vote;
            $posts[] = $post;
        }
        $this->data[ 'posts' ] = $posts;

        return $this;
    }

    private function paramsProcessing() {
        $account = $this->__getQuery( 'account' );
        if( is_null( $account ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $opinion = $this->__getQuery( 'opinion' );

        if( is_null( $opinion ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $start = intval( $this->__getQuery( 'start' ) );

        $rn = intval( $this->__getQuery( 'rn' ) );

        if( $rn == 0 ) $rn = 20;

        if( $rn > 100 ) $rn = 100;

        $this->params = array(
            'account' => $account,
            'opinion' => $opinion,
            'start' => $start,
            'rn' => $rn
        );

        return $this;
    }
}
