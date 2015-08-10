<?php

Class TopicAction extends \Local\BaseAction {

    private $data = array();

    private $topicModel;

    public function __execute() {

        $this->tpl = 'topic/topic';

        $this->paramsProcessing();

        $this->topicModel = new TopicModel();

        if( $this->getTopic() === false ) {
            $this->tpl = 'topic/none';
            return $this->data;
        }
        $this->getPoints()->reportReasons();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
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
            $point_data = $pointDataModel->get( $point['id'] );
            $point_data['index'] = \Local\Utils::pointIndex( $point_data );
            $point['data'] = $point_data;
        }

        $this->data['points'] = $points;
        return $this;

    }



    private function getTopic() {
        $id = $this->params[ 'id' ];

        $topic = $this->topicModel->get( $id );

        if( !$topic ) {
            return false;
        }

        $topicDataModel = new TopicDataModel();

        $topic_data = $topicDataModel->get( $topic[ 'id' ] );

        $categories = \Local\Utils::loadConf( 'categories', 'list' );

        $topic_data['cate'] = $categories[ $topic_data['cid'] ];

        $topic['data'] = $topic_data;

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
