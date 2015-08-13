<?php

Class CommentAction extends \Local\BaseAction {
    private $data = [];
    private $transactionModel;

    public function __execute() {
        $this->type = 'interface';

        // throw error is no account is in session
        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->getPost();

        

        // if this comment is reply another comment
        if( $this->params['reply_comment_id' ] != 0 ) {

            // get comment data
            $this->getReplyComment();

        }

        $this->transactionModel = new TransactionModel();

        $this->addComment()->sendMessage();

        $this->record( [
            'type' => 3,
            'relation_id' => $this->data['comment_id']
        ] );

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
        $account = $this->account;
        $post = $pool['post'];


        // send message to user who published this data
        // if the user who published the message is the user in session, don't send message to himself
        if( $account['id'] != $post['account_id'] ) {
            $this->sendCommentMessage();
        }

        if( $this->params['reply_comment_id' ] != 0 ) {
            $reply_comment = $pool['reply_comment'];
            // check if the user who sent this post and the user who sent this message is not the same one
            // then send message to the user to notice him/her someone replied his comment
            // if not check, the user will receive two messages
            if( ( $post['account_id'] != $reply_comment['account_id'] ) && ( $account['id'] != $reply_comment['account_id'] ) ) {

                $this->sendReplyMessage();
            }
        }

        return $this;
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

    private function sendReplyMessage() {
        $view = $this->getView();

        $conf = \Local\Utils::loadConf( 'message', 'reply' );

        $data = [
            'from' => 0,
            'to' => $this->pool['reply_comment']['account_id'],
            'type' => $conf->type,
            'title' => $view->render( $conf->template, array(
                '_part' => 'title',
                'account' => $this->account
            ) ),
            'content' => $view->render( $conf->template, array(
                '_part' => 'content',
                'account' => $this->account,
                'post' => $this->pool['post'],
                'comment' => array(
                    'content' => $this->params['content']
                )
            ) )
        ];

        $this->transactionModel->sendMessage( $data );

    }

    private function sendCommentMessage() {
        $view = $this->getView();

        $conf = \Local\Utils::loadConf( 'message', 'comment' );

        $data = [
            'from' => 0,
            'to' => $this->pool['post']['account_id'],
            'type' => $conf->type,
            'title' => $view->render( $conf->template, array(
                '_part' => 'title',
                'account' => $this->account
            ) ),
            'content' => $view->render( $conf->template, array(
                '_part' => 'content',
                'account' => $this->account,
                'post' => $this->pool['post'],
                'comment' => array(
                    'content' => $this->params['content']
                )
            ) )
        ];

        $this->transactionModel->sendMessage( $data );

    }

    private function addComment() {
        $params = $this->params;

        $comment_id = $this->transactionModel->addComment( array(
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
        $request = $this->request;

        $post_id = $request->getPost( 'post_id' );

        if( is_null( $post_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $content = $request->getPost( 'content' );

        if( is_null( $content ) || strlen( $content ) == 0 ) {
            $this->error( 'PARAMS_ERR' );
        }

        if( mb_strlen( $content ) > 400 ) {
            $this->error( 'PARAMS_ERR' );
        }

        $reply_comment_id = intval( $request->getPost( 'comment_id' ) );

        $this->params = [
            'post_id' => $post_id,
            'content' => $content,
            'reply_comment_id' => $reply_comment_id
        ];
        return $this;
    }
}
