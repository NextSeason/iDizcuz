<?php

Class ReportsAction extends \Local\MisAction {

    private $data = [];

    public function __execute() {
        $this->tpl = 'mis/reports';

        $this->paramsProcessing()->getReports()->getPosts()->getAccounts();

        $this->data['status'] = $this->params['status'];

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function getAccounts() {
        foreach( $this->data['reports'] as &$report ) {
            $target_account = \Accounts\Api::get( $report['target_account_id' ], [ 'id', 'uname' ] );
            $report['target_account'] = $target_account;
            $account = \Accounts\Api::get( $report[ 'account_id' ], [ 'id', 'uname' ] );
            $report['account'] = $account;
        }
        return $this;
    }

    private function getPosts() {
        foreach( $this->data['reports'] as &$report ) {
            $post = \Posts\Api::get( $report['post_id'] );

            if( $post ) {
                $post['data'] = \Posts\Api::getData( $report['post_id'] );
            }
            $report['post'] = $post;
        }
        return $this;
    }

    private function getReports() {
        $reportModel = new ReportModel();

        $where = [];

        if( !is_null( $this->params['status'] ) ) {
            $where[] = [ 'status', $this->params['status'] ];
        }

        $reports = $reportModel->select( [
            'where' => $where,
            'rn' => 30
        ] );

        $this->data['reports'] = $reports;
        return $this;
    }

    private function paramsProcessing() {
        $status = $this->__getQuery( 'status' );

        $this->params = [
            'status' => $status
        ];

        $this->data['status'] = $status;

        return $this;
    }
}
