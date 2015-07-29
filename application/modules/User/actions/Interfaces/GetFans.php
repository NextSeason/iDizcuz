<?php 

Class GetFansAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getFans()->getAccounts()->getTotal()->getTotal();

        return $this->data;
    }

    private function getTotal() {
        $accountDataModel = new AccountDataModel();
        $account_data = $accountDataModel->get( $this->params['account'] );
        if( !$account_data ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data['total'] = $account_data['fans'];
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

        foreach( $follows as $follow ) {
            $account_id = $follow['fans_id'];
            $account = $accountModel->get( $account_id, ['id', 'uname'] );

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

    private function getFans() {
        $followModel = new FollowModel();

        $params = $this->params;

        $follows = $followModel->getFansByAccount( [
            'account_id' => $params['account'],
            'columns' => [ 'fans_id' ],
            'start' => $params['start'],
            'rn' => $params['rn']
        ] );

        if( $follows === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->pool['follows'] = $follows;
        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $account = $request->getQuery( 'account' );

        $start = intval( $request->getQuery( 'start' ) );

        if( $start < 0 ) $start = 0;

        $rn = intval( $request->getQuery( 'rn' ) );

        if( $rn <= 0 ) $rn = 20;
        if( $rn > 100 ) $rn = 100;

        $this->params = [
            'account' => $account,
            'start' => $start,
            'rn' => $rn
        ];

        return $this;
    }
}
