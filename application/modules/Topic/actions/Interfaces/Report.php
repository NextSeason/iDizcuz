<?php

Class ReportAction extends \Local\BaseAction {
    private $data = array();
    private $postDataModel;
    private $reportModel;

    public function __execute() {
        $this->type = 'interface';

        if( is_null( $this->account ) ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing();

        $this->postDataModel = new PostDataModel();

        $this->checkPost();

        $this->reportModel = new ReportModel();

        $this->checkReport();

        $this->addReport();

        return $this->data();

    }

    private function addReport() {
        $report = $this->reportModel->insert( array(
            'post_id' => $this->params[ 'post_id' ],
            'account_id' => $this->account[ 'id' ],
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
        $post = $this->postDataModel->get( $this->params[ 'post_id' ] );
        if( !$post ) {
            $this->error( 'POST_NOTEXISTS' );
        }

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $post_id = $request->getPost( 'post_id' );

        if( is_null( $post_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $reason = $request->getPost( 'reason' );

        if( is_null( $reason ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $desc = $request->getPost( 'desc' );

        $this->params = [
            'post_id' => $post_id,
            'reason' => $reason,
            'desc' => $desc
        ];

        return $this;
    }
}
