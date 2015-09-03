<?php

Class VoteAction extends \Local\BaseAction {
    private $data = array();

    public function __execute() {
        $this->type = 'interface';

        if( is_null( $this->account ) ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->checkPost()->addVote()->sendMessage();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function sendMessage() {
        if( $this->params[ 'opinion' ] == 1 ) {
            \Message\Send::agreeMessage(
                $this->account['id'],
                $this->pool['post']['account_id'],
                array(
                    'post' => $this->pool['post']
                )
            );
        } else {
            \Message\Send::disagreeMessage(
                $this->account['id'],
                $this->pool['post']['account_id'],
                array(
                    'post' => $this->pool['post']
                )
            );
        }
        return $this;
    }

    private function addVote() {
        $params = $this->params;

        $voteModel = new VoteModel();

        $vote = $voteModel->getVoteByPostAndAccount( 
            $params[ 'post_id' ], 
            $this->account[ 'id' ],
            $params[ 'opinion' ],
            $params[ 'type' ]
        );

        if( $vote ) {
            $this->error( 'REACHED_MAX' );
        } else {

            $transactionModel = new TransactionModel();

            $transactionModel->vote( array(
                'post_id' => $params[ 'post_id' ],
                'account_id' => $this->account[ 'id' ],
                'opinion' => $params[ 'opinion' ],
                'type' => $params[ 'type' ],
                'value' => $params[ 'value' ]
            ),  $this->pool['post'] );
        }

        return $this;
    }

    private function checkPost() {
        $postModel = new PostModel();

        $post = $postModel->get( $this->params[ 'post_id' ] );

        if( !$post ) {
            $this->error( 'POST_NOTEXISTS' );
        }

        if( $this->account['id'] == $post['account_id'] ) {
            echo 'fdfkafsa';
            $this->error( 'PARAMS_ERR' );
        }

        $this->pool[ 'post' ] = $post;

        return $this;
    }

    private function paramsProcessing() {
        $post_id = $this->__getPost( 'post_id' );

        if( is_null( $post_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $opinion = $this->__getPost( 'opinion' );

        if( is_null( $opinion ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $type = $this->__getPost( 'type' );

        if( is_null( $type ) || !in_array( $type, [ 0, 1, 2 ] ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $value = intval( $this->__getPost( 'value' ) );

        if( $value <= 0 ) {
            $this->error( 'PARAMS_ERR' );
        }

        if( $type == 0 && $value != 1 ) {
            $this->error( 'PARAMS_ERR' );
        }

        if( ( $type == 1 || $type == 2 ) && $type > 20 ) {
            $this->error( 'PARAMS_ERR' );
        }

        $value = 1;

        $this->params = [
            'post_id' => $post_id,
            'opinion' => $opinion,
            'value' => $value,
            'type' => $type
        ];

        return $this;
    }
}
