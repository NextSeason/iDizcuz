<?php

Class ListAction extends \Local\BaseAction {
    private $data = array();

    private $rn = 20;
    private $topicModel;

    public function __execute() {
        $this->tpl = 'topic/list';

        $this->paramsProcessing();

        $this->topicModel = new TopicModel();

        $this->getTopics()->getTopicData();

        return $this->data;
    }

    private function getTopics() {
        $params = $this->params;
        $rn = $this->rn;

        $start = $rn * $params[ 'pn' ] + 1;
        
        $topics = $this->topicModel->getTopics();

        if( !$topics ) {
        }

        $this->data['topics'] = $topics;
        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $type = $request->getQuery( 'type' );

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
