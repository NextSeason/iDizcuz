<?php

Class TopicAction extends \Local\BaseAction {

    private $data = array();

    public function __execute() {

        $this->tpl = 'topic/topic';

        $this->paramsProcessing();

        if( $this->getTopic() === false ) {
            $this->tpl = 'topic/none';
            return $this->data;
        }
        $this->getPoints()->reportReasons();

        return $this->data;
    }

    public function __mobile() {
        $this->tpl = 'topicMobile/topic';
        $this->paramsProcessing();
        if( $this->getTopic() === false ) {
            $this->tpl = 'topicMobile/none';
            return $this->data;
        }
        $this->getPoints();
        return $this->data;
    }

    private function reportReasons() {
        $reportConf = \Local\Utils::loadConf( 'report', 'reasons' );
        $this->data[ 'reportReasons' ] = $reportConf;
        return $this;
    }
    private function getPoints() {
        $topic = $this->data['topic'];
        if( $topic['data']['type'] == 2 ) {
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

        $topicDataModel = new TopicDataModel();
        $topic_data = $topicDataModel->get( $id );

        if( !$topic_data || $topic_data['status'] == 0 ) return false;

        $categories = \Local\Utils::loadConf( 'categories', 'list' );
        $topic_data['cate'] = $categories[ $topic_data['cid'] ];

        $topicModel = new TopicModel();
        $topic = $topicModel->get( $id );
        $topic['data'] = $topic_data;

        $this->data[ 'topic' ] = $topic;

        return $this;
    }

    private function paramsProcessing() {
        
        $id = $this->__getParam('id');

        if( is_null( $id ) ) {
            $this->redirect( '/' );
            exit;
        }

        $this->params[ 'id' ] = $id;

        return $this;
    }
}
