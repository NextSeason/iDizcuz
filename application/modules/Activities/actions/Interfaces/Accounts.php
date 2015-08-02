<?php

Class AccountsAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getActivities()->getAccounts();

        return $this->data;
    }

    private function getAccounts() {
        $ids = $this->pool['ids'];
        $accountModel = new AccountModel();
        
        $accounts = $accountModel->gets( $ids );

        if( $accounts === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data[ 'accounts' ] = $accounts;

        return $this;
    }

    private function getActivities() {
        $params = $this->params;

        $activityModel = new ActivityModel();

        $where = [
            [ 'type', '5' ]
        ];

        if( $params['cursor'] != 0 ) {
            $where[] = [ 'id', '<', $params['cursor'] ];
        }

        $activities = $activityModel->select( [
            'where' => $where,
            'order' => [ [ 'id', 'DESC' ] ],
            'rn' => $params['rn']
        ] );

        if( $activities === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $ids = [];

        foreach( $activities as $activity ) {
            $ids[] = $activity['relation_id'];
        }

        $this->pool['ids'] = $ids;

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
