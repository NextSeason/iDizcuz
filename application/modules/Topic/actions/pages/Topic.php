<?php

Class TopicAction extends \Local\BaseAction {

    private $data = array();

    private $topicModel;

    public function __execute() {

        $this->tpl = 'topic/topic';

        $this->paramsProcessing();

        $this->topicModel = new TopicModel();

        $topic = $this->getTopic();

        return $this->data;
    }

    private function getTopic() {
        $id = $this->params[ 'id' ];
        if( $id ) {
            $topic = $this->topicModel->get( $id );

            if( !$topic ) {
                $topic = $this->topicModel->getCurrentFocus();
            }
        } else {
            $topic = $this->topicModel->getCurrentFocus();
        }

        $this->data[ 'topic' ] = $topic;
    }

    private function paramsProcessing() {
        
        $id = $this->request->getQuery( 'id' );

        $this->params[ 'id' ] = $id;

        return $this;
    }
}
