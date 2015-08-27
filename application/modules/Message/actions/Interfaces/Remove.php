<?php

Class RemoveAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->remove();

        return $this->data;
    }

    private function remove() {
        $transactionModel = new TransactionModel();

        $res = $transactionModel->removeReceivedMessage(
            $this->account['id'],
            $this->params['id']
        );
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
