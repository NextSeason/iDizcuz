<?php

Class RemoveCommentAction extends \Local\BaseAction {
    private $data = [];
    protected $type = 'interface';

    public function __execute() {
        if( is_null( $this->account ) ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->removeComment();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function removeComment() {
        $transactionModel = new TransactionModel();
        $res = $transactionModel->removeComment(
            $this->account['id'],
            $this->params['id']
        );
        if( !$res ) {
            $this->error( 'SYSTEM_ERR' );
        }

        return $this;
    }

    private function paramsProcessing() {
        $id = $this->request->getPost( 'id' );

        if( is_null( $id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params[ 'id' ] = $id;

        return $this;
    }
}
