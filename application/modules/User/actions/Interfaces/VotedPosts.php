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

        if( !$votes ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data[ 'votes' ] = $votes;

        return $this;
    }

    private function getPosts() {
        if( !count( $this->data[ 'votes' ] ) ) {
            return $this;
        }

        $postModel = new PostModel();
        $postDataModel = new PostDataModel();

        foreach( $this->data[ 'votes' ] as &$vote ) {
            $post = $postModel->get( $vote[ 'post_id' ] );
            $post[ 'data' ] = $postDataModel->get( $vote[ 'post_id' ] );
            $vote[ 'post' ] = $post;
        }

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $account = $request->getQuery( 'account' );
        if( is_null( $account ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $opinion = $request->getQuery( 'opinion' );

        if( is_null( $opinion ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $start = intval( $request->getQuery( 'start' ) );

        $rn = intval( $request->getQuery( 'rn' ) );

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
