<?php 

Class UnfollowAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->action();

        return $this->data;
    }

    private function action() {
        $transactionModel = new TransactionModel();

        $res = $transactionModel->unfollow( [
            'account_id' => $this->params['account_id'],
            'fans_id' => $this->account['id']
        ] );

        if( !$res ) {
            $this->error( 'SYSTEM_ERR' );
        }

        return $this;
    }

    private function paramsProcessing() {
        $account_id = $this->request->getPost( 'account_id' );

        if( is_null( $account_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = [
            'account_id' => $account_id
        ];

        return $this;
    }
}
