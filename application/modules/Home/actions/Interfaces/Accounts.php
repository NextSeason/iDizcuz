<?php

Class AccountsAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';
        $this->paramsProcessing()->getAccounts()->getFollowStatus();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function getFollowStatus() {
        if( count( $this->data['accounts'] ) == 0 ) {
            return $this;
        }
        $followModel = new FollowModel();

        foreach( $this->data['accounts'] as &$account ) {
            //$acccount['followed'    
        }
    }

    private function getAccounts() {
        $accountModel = new AccountModel();
        $where = [
            [ 'status', 0 ]
        ];
        if( $this->params['cursor'] != 0 ) {
            $where[] = [ 'id', '<', $this->params['cursor'] ];
        }
        $accounts = $accountModel->select( [
            'columns' => [ 'id', 'uname', 'img', 'sex' ],
            'where' => $where,
            'order' => [ [ 'id', 'DESC' ] ],
            'rn' => $this->params['rn']
        ] );

        if( $accounts === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data[ 'accounts' ] = $accounts;

        return $this;
    }

    private function paramsProcessing() {
        $cursor = intval( $this->request->getQuery('cursor') );
        if( $cursor < 0 ) $cursor = 0;

        $rn = intval( $this->request->getQuery( 'rn' ) );

        if( $rn < 1 ) $rn = 20;

        $this->params = [
            'cursor' => $cursor,
            'rn' => $rn
        ];

        return $this;
    }
}
