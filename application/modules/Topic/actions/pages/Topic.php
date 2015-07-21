<?php

Class TopicAction extends \Local\BaseAction {

    private $data = array();

    private $topicModel;

    public function __execute() {

        $this->tpl = 'topic/topic';

        $this->paramsProcessing();

        $this->topicModel = new TopicModel();

        $this->getTopic()->getPoints()->reportReasons();

        return $this->data;
    }

    private function reportReasons() {
        $reportConf = \Local\Utils::loadConf( 'report', 'reasons' );
        $this->data[ 'reportReasons' ] = $reportConf;
        return $this;
    }
    private function getPoints() {
        $topic = $this->data['topic'];
        if( $topic['data']['type'] == 0 ) {
            $this->data['points'] = [];
            return $this;
        }
        $points = $topic['points'];

        $pointModel = new PointModel();

        $points = $pointModel->gets( explode( ',', $points ) );

        if( !$points ) {
            //error
        }

        $pointDataModel = new PointDataModel();

        foreach( $points as &$point ) {
            $point['data'] = $pointDataModel->get( $point['id'] );
        }

        $this->data['points'] = $points;
        return $this;

    }



    private function getTopic() {
        $id = $this->params[ 'id' ];

        $topic = $this->topicModel->get( $id );

        $topicDataModel = new TopicDataModel();

        $topic['data'] = $topicDataModel->get( $topic[ 'id' ] );

        $this->data[ 'topic' ] = $topic;

        return $this;
    }

    private function paramsProcessing() {
        
        $id = $this->request->getParam( 'id' );

        if( is_null( $id ) ) {
            $this->redirect( '/' );
        }

        $this->params[ 'id' ] = $id;

        return $this;
    }
}
