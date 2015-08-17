<?php

Class WriteAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
    }

    public function __mobile() {
        $this->tpl = 'topicMobile/write';

        $this->paramsProcessing();

        if( $this->getTopic() === false ) {
            $this->tpl = 'topicMobile/none';
            return $this->data;
        }

        $this->getPoints();

        return $this->data;
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

        $this->data['points'] = $points;
        return $this;
    }

    private function getTopic() {
        $id = $this->params[ 'id' ];

        $topicDataModel = new TopicDataModel();

        $topic_data = $topicDataModel->select( [
            'columns' => [ 'id', 'type' ],
            'where' => [ 
                [ 'id', $id ],
                [ 'status', 1 ]
            ]
        ] );

        if( !$topic_data || !count( $topic_data ) ) return false;

        $topicModel = new TopicModel();

        $topic = $topicModel->get( $id, [ 'id', 'title', 'points' ] );

        if( !$topic ) return false;

        $topic['data'] = $topic_data[0];

        $this->data[ 'topic' ] = $topic;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $id = $request->getParam( 'id' );

        if( is_null( $id ) ) {
            $this->redirect( '/' );
        }

        $this->params['id'] = $id;

        return $this;
    }
}
