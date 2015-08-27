<?php

Class ReadAction extends \Local\BaseAction {
    private $data = [];
    protected $type = 'interface';

    public function __execute() {

        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }
        $this->paramsProcessing()->setRead();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
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
        $id = $this->__getPost( 'id' );

        if( is_null( $id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = [
            'id' => $id
        ];

        return $this;
    }
}
