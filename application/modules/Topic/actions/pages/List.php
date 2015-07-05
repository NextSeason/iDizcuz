<?php

Class ListAction extends \Local\BaseAction {
    private $data = array();
    private $rn = 20;

    public function __execute() {
        $this->tpl = 'topic/list';

        $this->paramsProcessing();

        $this->getTopics()->getTopicData();

        return $this->data;
    }

    private function getTopics() {

        $topicModel = new TopicModel();

        $params = $this->params;
        $rn = $this->rn;

        $start = $rn * $params[ 'pn' ] + 1;
        
        $topics = $topicModel->getTopics( $this->params[ 'type' ] );

        if( !$topics ) {
        }

        $this->data['topics'] = $topics;
        return $this;
    }

    private function getTopicData() {

        $topics = $this->data[ 'topics' ];

        if( count( $topics ) > 0 ) {
            $topicDataModel = new TopicDataModel();

            foreach( $topics as &$topic ) {
                $topic[ 'data' ] = $topicDataModel->get( $topic['id'] );
            }

            $this->data['topics'] = $topics;
        }

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $type = $request->getQuery( 'type' );

        $this->data['type'] = $type;

        $pn = $request->getQuery( 'pn' );

        if( is_null( $pn ) || !preg_match( '#^\d+$#', $pn ) ) {
            $pn = 0;
        }

        $this->params = array(
            'type' => $type,
            'pn' => $pn
        );

        return $this;
    }
}
