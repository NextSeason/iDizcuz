<?php

Class TopicsAction extends \Local\MisAction {
    private $data = [];
    private $topicModel;

    public function __execute() {
        $this->tpl = 'mis/topics';

        $this->paramsProcessing()->getTopicsData()->getTopics();

        return $this->data;
    }

    private function getTopicsData() {
        $params = $this->params;

        $topicDataModel = new TopicDataModel();

        $topics = $topicDataModel->adminGetTopicsData( [
            'type' => $params['type'],
            'start' => $params['start'],
            'rn' => $params['rn']
        ] );

        $this->pool[ 'topics_data' ] = $topics;

        return $this;

    }

    private function getTopics() {
        $topicModel = new TopicModel();

        $topics = [];

        foreach( $this->pool['topics_data'] as $topic_data ) {
            $topic = $topicModel->get( $topic_data['id'] );
            $topic['data'] = $topic_data;
            $topics[] = $topic;
        }

        $this->data['topics'] = $topics;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $type = $request->getQuery( 'type' );
        $start = intval( $request->getQuery( 'start' ) );
        $rn = $request->getQuery( 'rn' );
        if( $rn < 1 ) $rn = 20;

        $this->params = [
            'type' => $type,
            'start' => $start,
            'rn' => $rn
        ];
        return $this;
    }
}
