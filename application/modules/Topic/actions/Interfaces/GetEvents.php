<?php

Class GetEventsAction extends \Local\BaseAction {
    private $data = [];
    private $topicEventModel;

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing();

        $this->topicEventModel = new TopicEventModel();

        $this->getEvents();

        return $this->data;
    }

    private function getEvents() {
        $events = $this->params[ 'events' ];

        $events = $this->topicEventModel->gets( $events );

        if( !$events ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data[ 'events' ] = $events;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $events = $request->getQuery( 'events' );

        if( is_null( $events ) || !preg_match( '#(^\d[\d,]*\d$)|(^\d$)#', $events ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $events = explode( ',', $events );

        $this->params = [
            'events' => $events
        ];

        return $this;
    }
}
