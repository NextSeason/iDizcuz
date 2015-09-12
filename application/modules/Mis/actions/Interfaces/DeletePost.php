<?php

Class DeletePostAction extends \Local\MisAction {
    private $data = [];
    protected $type = 'interface';

    public function __execute() {
        $this->paramsProcessing()->deletePost()->dealReports();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function deletePost() {
        $params = $this->params;

        $postDataModel = new PostDataModel();

        $res = $postDataModel->update( [
            'set' => [ 'status' => $params['baleful'] == 0 ? 2 : 3 ],
            'where' => [
                [ 'id', $params['post_id'] ]
            ]
        ] );

        return $this;
    }

    private function dealReports() {
        if( $this->params['deal_reports'] == 1 ) {
            $reportModel = new ReportModel();
            $reportModel->update( [
                'set' => [ 'status' => 3 ],
                'where' => [
                    [ 'post_id', $this->params['post_id'] ]
                ]
            ] );
        }
        return $this;
    }

    private function paramsProcessing() {
        $post_id = $this->__getPost( 'post_id' );

        if( is_null( $post_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $baleful = intval( $this->__getPost( 'baleful' ) );

        $deal_reports = intval( $this->__getPost( 'deal_reports' ) );

        $this->params = [
            'post_id' => $post_id,
            'baleful' => $baleful,
            'deal_reports' => $deal_reports
        ];

        return $this;
    }
}
