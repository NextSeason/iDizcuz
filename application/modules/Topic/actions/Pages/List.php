<?php

Class ListAction extends \Local\BaseAction {
    private $data = array();
    private $rn = 20;

    public function __execute() {
        $this->tpl = 'topic/list';

        $this->paramsProcessing();

        $this->getTopicsData()->getTopics();

        return $this->data;
    }

    private function getTopics() {
        $topics_data = $this->pool['topics_data'];

        if( !count( $topics_data ) ) {
            $this->data['topics'] = [];
            return $this;
        }

        $topicModel = new TopicModel();

        $topics = [];

        foreach( $topics_data as $topic_data ) {
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

        $topics_data = $topicDataModel->getTopicsData( [
            'type' => $params['type'],
            'start' => $params['start'],
            'rn' => $this->rn
        ] );

        if( $topics_data === false ) {
            // err
        }

        $this->pool['topics_data'] = $topics_data;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $type = $request->getQuery( 'type' );

        $this->data['type'] = $type;

        $pn = intval( $request->getQuery( 'pn' ) );

        if( $pn < 1 ) $pn = 1;

        $start = ( $pn - 1 ) * $this->rn;

        $this->params = array(
            'type' => $type,
            'pn' => $pn,
            'start' => $start
        );

        return $this;
    }
}
