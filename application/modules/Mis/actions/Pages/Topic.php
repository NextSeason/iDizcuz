<?php

Class TopicAction extends \Local\MisAction {
    private $data = [];
    private $topicModel;

    public function __execute() {
        $this->tpl = 'mis/topic';

        $this->paramsProcessing();

        $this->topicModel = new TopicModel();

        $this->getTopic();

        return $this->data;
    }

    private function getTopic() {
        $topic = $this->topicModel->get( $this->params[ 'id' ] );
        $this->data[ 'topic' ] = $topic;
    }

    private function paramsProcessing() {
        $id = $this->request->getQuery( 'id' );

        if( is_null( $id ) ) {
            header( 'Location:/mis/page/newtopic' );
            exit;
        }

        $this->params[ 'id' ] = $id;
        return $this;
    }
}
