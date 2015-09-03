<?php

Class FollowAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        if( $this->account['data']['follow'] >= 1000 ) {
            $this->error( 'REACHED_MAX' );
        }

        $this->paramsProcessing()->check();

        $this->action()->sendMessage();

        $this->record( [
            'type' => 4,
            'relation_id' => $this->params['account_id']
        ] );

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function sendMessage() {
        \Message\Send::newFansMessage(
            $this->account['id'],
            $this->params['account_id']
        );
    }

    private function action() {
        $transactionModel = new TransactionModel();

        $follow = $transactionModel->follow( [
            'account_id' => $this->params[ 'account_id' ],
            'fans_id' => $this->account['id']
        ] );

        if( !$follow ) {
            $this->error( 'SYSTEM_ERR' );
        }

        return $this;
    }

    private function check() {

        if( $this->account['id'] == $this->params['account_id'] ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $followModel = new FollowModel();

        $followStatus = $followModel->getFollowStatus([
            'account_id' => $this->params['account_id'],
            'fans_id' => $this->account['id']
        ]);

        if( $followStatus ) {
            $this->error( 'SYSTEM_ERR' );
        }


        $accountDataModel = new AccountDataModel();

        $account_data = $accountDataModel->get( $this->params['account_id'], ['id'] );

        if( !$account_data ) {
            $this->error( 'ACCOUNT_NOTEXISTS' );
        }

        return $this;
    }

    private function paramsProcessing() {
        $account_id = $this->__getPost( 'account_id' );

        if( is_null( $account_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = [
            'account_id' => $account_id
        ];

        return $this;
    }
}
