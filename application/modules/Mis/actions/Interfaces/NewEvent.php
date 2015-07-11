<?php

Class NewEventAction extends \Local\MisAction {
    private $data = [];
    private $topicEventModel;

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing();

        $this->topicEventModel = new TopicEventModel();

        $this->addEvent();

        return $this->data;
    }

    private function addEvent() {
        $params = $this->params;

        $data = [
            'title' => $params[ 'title' ],
            'content' => $params[ 'content' ],
            'img' => $params[ 'img' ]
        ];

        if( !is_null( $params[ 'tip' ] ) ) {
            $data[ 'tip' ] = $params[ 'tip' ];
        }

        if( !is_null( $params[ 'time' ] ) ) {
            $data[ 'time' ] = $params[ 'time' ];
        }

        if( !is_null( $params[ 'origin' ] ) ) {
            $data[ 'origin' ] = $params[ 'origin' ];
        }

        if( !is_null( $params[ 'origin_url' ] ) ) {
            $data[ 'origin_url' ] = $params[ 'origin_url' ];
        }

        if( !is_null( $params[ 'origin_logo' ] ) ) {
            $data[ 'origin_logo' ] = $params[ 'origin_logo' ];
        }

        $topicEvent = $this->topicEventModel->insert( $data );

        $this->data[ 'topicEvent' ] = $topicEvent;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $title = $request->getPost( 'title' );

        if( is_null( $title ) ) {
            $this->error( 'PARAMS_ERR', 'event title is required' ); 
        }
        
        $img = $request->getPost( 'img' );

        if( is_null( $img ) ) {
            $this->error( 'PARAMS_ERR', 'event title is required' ); 
        }

        $tip = $request->getPost( 'tip' );

        $time = $request->getPost( 'time' );

        $origin = $request->getPost( 'origin' );

        $origin_url = $request->getPost( 'origin_url' );

        $origin_logo = $request->getPost( 'origin_logo' );

        $content = $request->getPost( 'content' );

        if( is_null( $content ) ) {
            $this->error( 'PARAMS_ERR', 'event content is required' );
        }

        $this->params = [
            'title' => $title,
            'img' => $img,
            'tip' => $tip,
            'time' => $time,
            'origin' => $origin,
            'origin_url' => $origin_url,
            'origin_logo' => $origin_logo,
            'content' => $content
        ];

        return $this;
    }
}
