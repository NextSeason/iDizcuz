<?php

Class ListAction extends \Local\BaseAction {
    private $data = [];
    protected $type = 'interface';

    public function __execute() {
        $this->paramsProcessing()->getTopicsData()->getTopics()->getPoints();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
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

        if( $params['cursor'] != 0 ) {
            $where[] = [ 'id', '<', $params[ 'cursor' ] ];
        }

        $topics_data = $topicDataModel->select( [
            'where' => $where,
            'order' => [ [ 'id', 'DESC' ] ],
            'rn' => $params[ 'rn' ]
        ] );

        if( $topics_data === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->pool[ 'topics_data' ] = $topics_data;

        return $this;
    }

    private function paramsProcessing() {
        $cid = intval( $this->__getQuery( 'cid' ) );

        $rn = intval( $this->__getQuery( 'rn' ) );

        if( $rn <= 0 || $rn > 100 ) $rn = 20;

        $cursor = intval( $this->__getQuery( 'cursor' ) );
        if( $cursor < 0 ) $cursor = 0;

        $this->params = [
            'cid' => $cid,
            'cursor' => $cursor == 0 ? 0 : \Local\Utils::decodeId( $cursor ),
            'rn' => $rn
        ];
        return $this;
    }
}
