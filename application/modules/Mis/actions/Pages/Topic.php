<?php

Class TopicAction extends \Local\MisAction {
    private $data = [
        'points' => []
    ];
    private $topicModel;
    private $topic = [
        'id' => 0,
        'status' => 0,
        'title' => '',
        'type' => 1,
        'cid' => 1,
        'image' => '',
        'desc' => ''
    ];

    public function __execute() {
        $this->tpl = 'mis/topic';

        $this->data['topic'] = $this->topic;

        $this->paramsProcessing()->getCategories();

        if( $this->params['id'] != 0 ) {
            $this->getTopic()->getTopicData();
            if( $this->data['topic']['data']['type'] == 1 ) {
                $this->getPoints();
            }
        }

        return $this->data;
    }

    private function getPoints() {
        $points_id = $this->data['topic']['points'];

        if( !strlen( $points_id ) ) {
            $this->data['points'] = [];
            return $this;
        }
        $pointModel = new PointModel();

        $points = $pointModel->gets( explode( ',', $points_id ) );

        $this->data['points'] = $points;
        return $this;
    }

    private function getTopicData() {
        $topicDataModle = new TopicDataModel();
        $topic_data = $topicDataModle->get( $this->data['topic']['id'] );
        $this->data[ 'topic' ]['data'] = $topic_data;
        return $this;
    }

    private function getCategories() {
        $categories = \Local\Utils::loadConf( 'categories', 'list' );
        $this->data['categories'] = $categories;
        return $this;
    }

    private function getTopic() {
        $topicModel = new TopicModel();
        $topic = $topicModel->get( $this->params[ 'id' ] );
        $this->data[ 'topic' ] = $topic;
        return $this;
    }

    private function paramsProcessing() {
        $id = $this->__getQuery( 'id' );
        $this->params[ 'id' ] = $id;
        return $this;
    }
}
