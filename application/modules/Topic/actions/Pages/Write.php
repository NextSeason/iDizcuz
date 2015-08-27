<?php

Class WriteAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {

        $this->paramsProcessing();

        $params = $this->params;

        if( !is_null( $params['post'] ) ) {
            $this->redirect( '/post/' . $params['post'] );
            exit;
        }

        if( !is_null( $params['topic'] ) ) {
            $this->redirect( '/topic/' . $params['topic'] );
            exit;
        }

        $this->redirect( '/' );
        exit;
    }

    public function __mobile() {
        $this->tpl = 'topicMobile/write';

        $this->paramsProcessing()->getPost()->getTopic()->getPoints();

        return $this->data;
    }

    /**
     * get data of post in view of
     */
    private function getPost() {
        if( is_null( $this->params['post'] ) ) {
            return $this;
        }

        $postModel = new PostModel();
        $post = $postModel->get( $this->params[ 'post' ], [ 'id', 'title', 'topic_id' ] );

        $this->data[ 'post' ] = $post;

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

        $this->data['points'] = $points;
        return $this;
    }

    private function getTopic() {
        if( isset( $this->data[ 'post' ] ) ) {
            $id = $this->data['post']['topic_id'];
        } else if( !is_null( $this->params['topic' ] ) ) {
            $id = $this->params['topic'];
        }

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
        $topic = $this->__getQuery( 'topic' );

        $post = $this->__getQuery( 'post' );

        if( is_null( $topic ) && is_null( $post ) ) {
            $this->redirect( '/' );
            exit;
        }

        $this->params = [
            'topic' => $topic,
            'post' => $post
        ];

        return $this;
    }
}
