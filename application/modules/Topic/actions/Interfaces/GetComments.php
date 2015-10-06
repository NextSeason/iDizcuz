<?php

Class GetCommentsAction extends \Local\BaseAction {

    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getComments()->getTotal()->getAccounts()->getReplyAccount();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
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
            $comment['content'] = nl2br( $comment['content'] );
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
            $comment['own'] = $this->account && $comment['account_id'] == $this->account['id'] ? 1 : 0;
        }

        return $this;
    }

    private function getComments() {
        $params = $this->params;

        $commentModel = new CommentModel();

        $where = [
            [ 'post_id', $params[ 'post_id' ] ],
        ];

        if( $params[ 'cursor' ] != 0 ) {
            $where[] = [ 'id', '<', $params[ 'cursor' ] ];
        }

        $comments = $commentModel->select( [
            'where' => $where,
            'order' => [ [ 'id', 'DESC' ] ],
            'rn' => $params[ 'rn' ]
        ] );

        if( $comments === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data[ 'comments' ] = $comments;
        return $this;
    }

    private function paramsProcessing() {
        $post_id = $this->__getQuery( 'post_id' );

        if( is_null( $post_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $cursor = intval( $this->__getQuery( 'cursor' ) );

        $rn = intval( $this->__getQuery( 'rn' ) );

        if( $rn == 0 ) $rn = 20;
        if( $rn > 100 ) $rn = 100;

        $this->params = [
            'post_id' => $post_id,
            'cursor' => $cursor == 0 ? 0 : \Local\Utils::decodeId( $cursor ),
            'rn' => $rn
        ];

        return $this;
    }
}
