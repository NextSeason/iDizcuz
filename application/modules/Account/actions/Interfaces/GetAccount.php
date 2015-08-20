<?php

Class GetAccountAction extends \Local\BaseAction {
    private $data = [];
    protected $type = 'interface';

    public function __execute() {
        $this->paramsProcessing()->getAccount();

        if( $this->account ) {
            if( $this->account['id'] == $this->data['user']['id'] ) {
                $this->data['user']['self'] = 1;
            } else {
                $this->getFollowStatus();
            }
        }
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function getFollowStatus() {
        $followModel = new FollowModel();

        $follow = $followModel->getFollowStatus( [
            'account_id' => $this->data['user']['id'],
            'fans_id' => $this->account['id']
        ] );

        $this->data['user']['followed'] = $follow ? 1 : 0;

        return $this;
    }

    private function getAccount() {
        $account_id = $this->params['account_id'];

        $accountModel = $this->accountModel ? $this->accountModel : new AccountModel();
        $account = $accountModel->get( $account_id );

        if( !$account ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $account['followed'] = 0;
        $account['self'] = 0;

        $accountDataModel = new AccountDataModel();

        $account_data = $accountDataModel->get( $account_id, [
            'post_cnt', 'agree', 'disagree', 'mark', 'follow', 'fans'
        ] );

        if( !$account_data ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $account['data'] = $account_data;

        $industries = \Local\Utils::loadConf( 'industries', 'list' );

        if( $account['industry'] != 0 ) {
            $account[ 'industry' ] = trim( $industries[ $account[ 'industry' ] ], '-' );
        }

        $this->data['user'] = $account;

        return $this;
    }

    private function paramsProcessing() {
        $account_id = $this->request->getQuery( 'account_id' );

        $this->params = [
            'account_id' => $account_id     
        ];

        return $this;
    }
}
