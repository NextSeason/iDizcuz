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

        $this->transactionModel = new TransactionModel();
            
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
        $view = $this->getView();

        $conf = \Local\Utils::loadConf( 'message', 'newfans' );

        $data = [
            'from' => 0,
            'to' => $this->params['account_id'],
            'type' => $conf->type,
            'title' => $view->render( $conf->template, [
                'account' => $this->account
            ] ),
            'content' => $this->account['id']
        ];

        $this->transactionModel->sendMessage( $data );
    }

    private function action() {
        $follow = $this->transactionModel->follow( [
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
