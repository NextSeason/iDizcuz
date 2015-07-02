<?php

Class TopicAction extends \Local\BaseAction {

    private $data = array();

    private $topicModel;

    public function __execute() {

        $this->tpl = 'topic/topic';

        $this->paramsProcessing();

        $this->topicModel = new TopicModel();

        $this->getTopic()->getEvents()->reportReasons();

        return $this->data;
    }

    private function reportReasons() {
        $reportConf = \Local\Utils::loadConf( 'report', 'reasons' );
        $this->data[ 'reportReasons' ] = $reportConf;
        return $this;
    }

    private function getEvents() {
        $events = $this->data[ 'topic' ][ 'events' ];

        if( strlen( $events ) == 0 ) { 
            $this->data[ 'topic' ][ 'events' ] = array();
            return $this; 
        }

        $events = explode( ',', $events );

        $topicEventModel = new TopicEventModel();

        $list = array();

        foreach( $events as $event ) {
            $list[] = $topicEventModel->get( $event );
        }

        $this->data[ 'topic' ][ 'events' ] = $list;
        
        return $this;
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

        return $this;
    }

    private function paramsProcessing() {
        
        $id = $this->request->getQuery( 'id' );

        $this->params[ 'id' ] = $id;

        return $this;
    }
}
