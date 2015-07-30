<?php

Class ListAction extends \Local\BaseAction {
    private $data = array();
    private $rn = 20;

    public function __execute() {
        $this->tpl = 'topic/list';

        $this->paramsProcessing();

        $this->getTopicsData()->getTopics()->getPoints();

        $this->data['cid'] = $this->params['cid'];

        return $this->data;
    }

    private function getPoints() {

        $pointModel = new PointModel();
        $pointDataModel = new PointDataModel();

        foreach( $this->data['topics'] as &$topic ) {
            if( $topic['data']['type'] == 1 ) {
                $points = explode( ',', $topic['points'] );
                $topic['points'] = [];
                foreach( $points as $id ) {
                    $point = $pointModel->get( $id );
                    $point['data'] = $pointDataModel->get( $id );
                    $point['data']['index'] = \Local\Utils::pointIndex( $point['data']);
                    $topic[ 'points' ][] = $point;
                }
            }
        }

        return $this;
    }

    private function getTopics() {
        $topics_data = $this->pool['topics_data'];

        if( !count( $topics_data ) ) {
            $this->data['topics'] = [];
            return $this;
        }

        $topicModel = new TopicModel();

        $topics = [];

        $categories = \Local\Utils::loadConf( 'categories', 'list' );

        foreach( $topics_data as $topic_data ) {
            $topic_data[ 'cate' ] = $categories[ $topic_data['cid'] ];
            $topic = $topicModel->get( $topic_data['id'] );
            $topic['data'] = $topic_data;
            $topics[] = $topic;
        }

        $this->data['topics'] = $topics;
        return $this;
    }

    private function getTopicsData() {
        $params = $this->params;

        $topicDataModel = new TopicDataModel();

        $where = [ [ 'status', 1 ] ];

        if( $params[ 'cid' ] != 0 ) {
            $where[] = [ 'cid', $params['cid'] ];
        }

        $topics_data = $topicDataModel->select( [
            'where' => $where,
            'order' => [ [ 'id', 'DESC' ] ],
            'start' => $params['start'],
            'rn' => $params['rn']
        ] );

        if( $topics_data === false ) {
            // err
        }

        $this->pool['topics_data'] = $topics_data;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $cid = intval( $request->getParam('cid') );

        $pn = intval( $request->getParam( 'pn' ) );

        if( $pn < 1 ) $pn = 1;

        $start = ( $pn - 1 ) * $this->rn;

        $this->params = array(
            'cid' => $cid,
            'pn' => $pn,
            'start' => $start,
            'rn' => 20
        );

        return $this;
    }
}
