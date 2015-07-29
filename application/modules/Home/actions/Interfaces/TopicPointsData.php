<?php

Class TopicPointsDataAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getPoints()->getPointsData();
        return $this->data;
    }

    private function getPointsData() {
        if( !isset( $this->pool['points'] ) ) {
            $this->data['points'] = [];
            return $this;
        }

        $points_data = [];

        $pointDataModel = new PointDataModel();

        foreach( $this->pool['points'] as $id ) {
            $points_data[] = $pointDataModel->get( $id );
        }

        $this->data['points_data'] = $points_data;
        
        return $this;
    }

    private function getPoints() {
        $topicModel = new TopicModel();

        $topic = $topicModel->get( $this->params['id'] );

        if( !$topic ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $points = $topic['points'];

        if( $points == '' ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $points = explode( ',', $points );

        $this->pool['points'] = $points;

        return $this;
    }

    private function paramsProcessing() {
        $id = $this->request->getQuery( 'id' );

        if( is_null( $id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = [
            'id' => $id
        ];

        return $this;
    }
}
