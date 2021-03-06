<?php

Class GetFollowsAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getFollows()->getAccounts()->getTotal();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function getTotal() {
        $accountDataModel = new AccountDataModel();
        $account_data = $accountDataModel->get( $this->params['account_id'] );
        if( !$account_data ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data['total'] = $account_data['follow'];
        return $this;
    }

    private function getAccounts() {
        $follows = $this->pool['follows'];

        if( !count( $follows ) ) {
            $this->data['accounts'] = [];
            return $this;
        }

        $accountModel = $this->accountModel ? $this->accountModel : new AccountModel();
        $accountDataModel = new AccountDataModel();
        $followModel = new FollowModel();

        $accounts = [];

        $industries = \Local\Utils::loadConf( 'industries', 'list' );

        foreach( $follows as $follow ) {
            $account_id = $follow['account_id'];

            $account = $accountModel->get( $account_id, [ 'id', 'uname', 'industry', 'employment', 'position', 'img', 'sex', 'desc' ] );

            if( $account['industry'] != 0 ) {
                $account['industry'] = trim( $industries[ $account['industry'] ], '-' );
            }

            $account['data'] = $accountDataModel->get( $account_id, [
                'post_cnt', 'agree', 'disagree', 'score', 'mark', 'fans', 'follow'
            ] );

            if( !$this->account ) {
                $account['followed'] = 0;
                $account['self'] = 0;
            } else {

                $account['self'] = $this->account['id'] == $account_id ? 1 : 0; 

                $followStatus = $followModel->getFollowStatus( [
                    'account_id' => $account_id,
                    'fans_id' => $this->account['id']
                ] );


                $account['followed'] = $followStatus ? 1 : 0;
            }

            $accounts[] = $account;
        }

        $this->data['accounts'] = $accounts;

        return $this;
    }

    private function getFollows() {
        $followModel = new FollowModel();

        $params = $this->params;

        $follows = $followModel->getFollowsByAccount( [
            'fans_id' => $params['account_id'],
            'columns' => [ 'account_id' ],
            'start' => $params['start'],
            'rn' => $params['rn']
        ] );

        if( $follows === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->pool[ 'follows' ] = $follows;
        return $this;
    }

    private function paramsProcessing() {
        $account_id = $this->__getQuery( 'account_id' );

        $start = intval( $this->__getQuery( 'start' ) );

        if( $start < 0 ) $start = 0;

        $rn = intval( $this->__getQuery( 'rn' ) );

        if( $rn <= 0 ) $rn = 20;
        if( $rn > 100 ) $rn = 100;

        $this->params = [
            'account_id' => $account_id,
            'start' => $start,
            'rn' => $rn
        ];

        return $this;
    }
}
