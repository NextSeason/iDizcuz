<?php

Class ReadAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->setRead();
        return $this->data;
    }

    private function setRead() {
        $transactionModel = new TransactionModel();

        $res = $transactionModel->readMessage(
            $this->account[ 'id' ],
            $this->params[ 'id' ]
        );

        if( $res === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        return $this;
    }

    private function paramsProcessing() {
        $id = $this->request->getPost( 'id' );

        if( is_null( $id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = [
            'id' => $id
        ];

        return $this;
    }
}
