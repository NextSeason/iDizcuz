<?php

Class ReportAction extends \Local\BaseAction {
    private $data = array();
    private $reportModel;

    public function __execute() {
        $this->type = 'interface';

        if( is_null( $this->account ) ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->checkPost();

        $this->reportModel = new ReportModel();

        $this->checkReport()->addReport();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function addReport() {
        $report = $this->reportModel->insert( array(
            'post_id' => $this->params[ 'post_id' ],
            'account_id' => $this->account[ 'id' ],
            'target_account_id' => $this->pool['post_data']['account_id'],
            'reason' => $this->params[ 'reason' ],
            'desc' => $this->params[ 'desc' ]
        ) );

        if( !$report ) {
            $this->error( 'SYSTEM_ERR' );
        }
        return $this;

    }

    private function checkReport() {
        $params = $this->params;

        $report = $this->reportModel->getReportByPostAndAccount(  
            $params[ 'post_id' ],
            $this->account[ 'id' ] 
        );

        if( $report ) {
            $this->error( 'REPORTED_POST' );
        }

        return $this;
    }

    private function checkPost() {
        $postDataModel = new PostDataModel();

        $post_data = $postDataModel->get( $this->params[ 'post_id' ] );
        if( !$post_data ) {
            $this->error( 'POST_NOTEXISTS' );
        }

        $this->pool[ 'post_data' ] = $post_data;

        return $this;
    }

    private function paramsProcessing() {
        $post_id = $this->__getPost( 'post_id' );

        if( is_null( $post_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $reason = $this->__getPost( 'reason' );

        if( is_null( $reason ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $desc = $this->__getPost( 'desc' );

        $this->params = [
            'post_id' => $post_id,
            'reason' => $reason,
            'desc' => $desc
        ];

        return $this;
    }
}
