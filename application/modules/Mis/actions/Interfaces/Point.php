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

        $res = $pointModel->update( [
            'set' => [
                'title' => $this->params['title'],
                'desc' => $this->params['desc']
            ],
            'where' => [
                ['id', $this->params['id'] ]
            ]
        ] );

        if( !$res ) {
            $this->error( 'SYSTEM_ERR' );
        }
        return $this;
    }

    private function paramsProcessing() {
        $id = $this->__getPost( 'id' );

        $title = $this->__getPost('title');

        if( is_null( $title ) || strlen( $title ) == 0 ) {
            $this->error( 'PARAMS_ERR' );
        }

        $desc = $this->__getPost( 'desc' );

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
