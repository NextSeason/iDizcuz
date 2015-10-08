<?php

Class RemovePostAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        if( is_null( $this->account ) ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->removePost();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function removePost() {
        $transactionModel = new TransactionModel();

        if( $this->params[ 'trash' ] == 1 ) { 
            $res = $transactionModel->trashPost(
                $this->account[ 'id' ],
                $this->params[ 'post_id' ]
            );
        } else {
            $res = $transactionModel->removePost(
                $this->account[ 'id' ],
                $this->params[ 'post_id' ]
            );
        }

        if( $res === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        return $this;
    }

    private function paramsProcessing() {
        $post_id = $this->__getPost( 'post_id' );

        if( is_null( $post_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $trash = $this->__getPost( 'trash' );

        if( is_null( $trash ) || $trash != 0 ) {
            $trash = 1;
        }

        $this->params = [ 
            'post_id' => $post_id,
            'trash' => $trash
        ];

        return $this;
    }
}
