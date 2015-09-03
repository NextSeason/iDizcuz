<?php

Class CommentAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->getPost();

        if( $this->params['reply_comment_id' ] != 0 ) {
            $this->getReplyComment();

        }

        $this->addComment()->sendMessage();

        $this->data['account'] = [
            'id' => $this->account[ 'id' ],
            'uname' => $this->account[ 'uname' ]
        ];

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function sendMessage() {
        $pool = $this->pool;

        if( $this->params['reply_comment_id' ] != 0 && $this->account['id'] != $pool['reply_comment']['account_id'] ) {
            \Message\Send::replyCommentMessage(
                $this->account['id'],
                $pool['reply_comment']['account_id'],
                array(
                    'post' => $pool['post'],
                    'content' => $this->params['content']
                )
            );
            return $this;
        }

        if( $this->account['id'] != $pool['post']['account_id'] ) {
            \Message\Send::commentMessage(
                $this->account['id'], 
                $pool['post']['account_id'],
                array(
                    'post' => $pool['post'],
                    'content' => $this->params['content']
                )
            );

            return $this;
        }
    }

    private function getPost() {
        $postModel = new PostModel();

        $post = $postModel->get( $this->params[ 'post_id' ] );

        if( !$post ) {
            $this->error( 'POST_NOTEXISTS' );
        }

        $this->pool[ 'post' ] = $post;

        return $this;
    }

    private function getReplyComment() {
        $commentModel = new CommentModel();

        $comment = $commentModel->get( $this->params[ 'reply_comment_id' ] );

        if( !$comment ) {
            $this->error( 'COMMENT_NOTEXISTS' );
        }

        $this->pool[ 'reply_comment' ] = $comment;

        return $this;
    }

    private function addComment() {
        $params = $this->params;

        $transactionModel = new TransactionModel();

        $comment_id = $transactionModel->addComment( array(
            'post_id' => $params[ 'post_id' ],
            'content' => $params[ 'content' ],
            'reply_account_id' => $params[ 'reply_comment_id' ] == 0 ? 0 : $this->pool[ 'reply_comment' ][ 'account_id' ],
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
        $post_id = $this->__getPost( 'post_id' );

        if( is_null( $post_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $content = $this->__getPost( 'content' );

        if( is_null( $content ) || strlen( $content ) == 0 ) {
            $this->error( 'PARAMS_ERR' );
        }

        if( mb_strlen( $content ) > 400 ) {
            $this->error( 'PARAMS_ERR' );
        }

        $reply_comment_id = intval( $this->__getPost( 'comment_id' ) );

        $this->params = [
            'post_id' => $post_id,
            'content' => $content,
            'reply_comment_id' => $reply_comment_id
        ];
        return $this;
    }
}
