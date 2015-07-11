<?php

Class CommentAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        if( !$this->account ) {
            $this->error( 'NOT_LOGIN' );
        }

        $this->paramsProcessing();

        $this->addComment();

        return $this->data;
    }

    private function addComment() {
        $params = $this->params;

        $transactionModel = new TransactionModel();

        $comment_id = $transactionModel->addComment( array(
            'post_id' => $params[ 'post_id' ],
            'content' => $params[ 'content' ],
            'reply_account_id' => $params[ 'reply_account_id' ],
            'reply_comment_id' => $params[ 'reply_comment_id' ],
            'account_id' => $this->account[ 'id' ],
            'ip' => ip2long( $_SERVER[ 'REMOTE_ADDR' ] )
        ) );

        if( !$comment_id ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data[ 'comment_id' ] = $comment_id;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $post_id = $request->getPost( 'post_id' );

        if( is_null( $post_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $content = $request->getPost( 'content' );

        if( is_null( $content ) ) {
            $this->error( 'PARAMS_ERR' );
        }
        if( strlen( $content ) == 0 ) {
        }

        if( mb_strlen( $content ) > 400 ) {
            $this->error( 'PARAMS_ERR' );
        }

        $reply_account_id = intval( $request->getPost( 'account_id' ) );

        $reply_comment_id = intval( $request->getPost( 'comment_id' ) );

        $this->params = [
            'post_id' => $post_id,
            'content' => $content,
            'reply_account_id' => $reply_account_id,
            'reply_comment_id' => $reply_comment_id
        ];


        return $this;
    }
}
