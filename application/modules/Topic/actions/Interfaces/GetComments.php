<?php

Class GetCommentsAction extends \Local\BaseAction {

    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getComments()->getTotal()->getAccounts()->getReplyAccount();

        return $this->data;
    }

    private function getTotal() {
        $postDataModel = new PostDataModel();

        $post_data = $postDataModel->get( $this->params['post_id'] );

        if( !$post_data ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data['total'] = $post_data['comments_cnt'];

        return $this;
    }

    private function getReplyAccount() {
        if( count( $this->data[ 'comments'] ) == 0 ) {
            return $this;
        }
        
        $accountModel = $this->accountModel ? $this->accountModel : new AccountModel();

        foreach( $this->data[ 'comments' ] as &$comment ) {
            if( $comment[ 'reply_account_id' ] == 0 ) continue;
            $comment[ 'reply_account' ] = $accountModel->get( $comment[ 'reply_account_id' ], [ 'id', 'uname' ] );
        }

        return $this;

    }

    private function getAccounts() {
        if( count( $this->data[ 'comments'] ) == 0 ) {
            return $this;
        }

        $accountModel = $this->accountModel ? $this->accountModel : new AccountModel();

        foreach( $this->data[ 'comments'] as &$comment ) {
            $comment[ 'account' ] = $accountModel->get( $comment[ 'account_id' ], [ 'id', 'uname' ] );
        }

        return $this;
    }

    private function getComments() {
        $params = $this->params;

        $commentModel = new CommentModel();

        $comments = $commentModel->getCommentsByPost(
            $params[ 'post_id' ],
            $params[ 'start' ],
            $params[ 'rn' ]
        );

        if( $comments === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data[ 'comments' ] = $comments;
        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $post_id = $request->getQuery( 'post_id' );

        if( is_null( $post_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $start = intval( $request->getQuery( 'start' ) );

        $rn = intval( $request->getQuery( 'rn' ) );

        if( $rn == 0 ) $rn = 20;
        if( $rn > 100 ) $rn = 100;

        $this->params = [
            'post_id' => $post_id,
            'start' => $start,
            'rn' => $rn
        ];

        return $this;
    }
}
