<?php

Class PointAction extends \Local\MisAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing();

        if( is_null( $this->params['id'] ) || $this->params['id'] == 0 ) {
            $this->addPoint();
        } else {
            $this->updatePoint();
        }

        return $this->data;
    }

    private function addPoint() {
        $transactionModel = new TransactionModel();

        $point_id = $transactionModel->addPoint( [
            'title' => $this->params['title'],
            'desc' => $this->params['desc']
        ] );

        if( !$point_id ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data['point_id'] = $point_id;

        return $this;
    }

    private function updatePoint() {
        $pointModel = new PointModel();

        $res = $pointModel->update( $this->params['id'], [
            'title' => $this->params['title'],
            'desc' => $this->params['desc']
        ] );

        if( !$res ) {
            $this->error( 'SYSTEM_ERR' );
        }
        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $id = $request->getPost( 'id' );

        $title = $request->getPost('title');

        if( is_null( $title ) || strlen( $title ) == 0 ) {
            $this->error( 'PARAMS_ERR' );
        }

        $desc = $request->getPost( 'desc' );

        if( is_null( $desc ) || strlen( $desc ) == 0 ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = [
            'id' => $id,
            'title' => $title,
            'desc' => $desc
        ];

        return $this;
    }
}
