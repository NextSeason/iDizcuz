<?php

Class ReportAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'mis/report';

        $this->paramsProcessing()->getReport()->getPost()->getTargetAccount()->getAccount();

        echo '<pre>';
        print_r( $this->data );
        echo '</pre>';

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function getAccount() {
        $report_account = \Accounts\Api::get( $this->data['report']['account_id'] );
        $this->data['report_account'] = $report_account;
        return $this;
    }

    private function getTargetAccount() {
        $target_account = \Accounts\Api::get( $this->data['report']['target_account_id'] );
        $this->data['target_account'] = $target_account;
        return $this;
    }

    private function getPost() {
        $post = \Posts\Api::get( $this->data['report']['id'] );
        $this->data['post'] = $post;
        return $this;
    }

    private function getReport() {
        $reportModel = new ReportModel();

        if( $this->params['report_id'] ) {
            $report = \Reports\Api::get( $this->params['report_id'] );
        } else {
            $report = \Reports\Api::firstUntreatedReport();
        }

        $this->data['report'] = $report;
        return $this;
    }

    private function paramsProcessing() {
        $report_id = $this->__getQuery( 'id' );

        $this->params = [
            'report_id' => $report_id
        ];

        return $this;
    }
}
