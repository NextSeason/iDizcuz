<?php

Class MessagesAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'message/messages';

        if( !$this->account ) {
            $this->redirect( '/signin' );
        }

        $this->paramsProcessing()->getMessages();

        $this->data[ 'tab' ] = $this->params[ 'tab' ];

        return $this->data;
    }

    private function getMessages() {
        $params = $this->params;

        $messageModel = new MessageModel();

        $messages = $messageModel->getAccountReceivedSystemMessagesByType(
            $this->account[ 'id' ],
            $params[ 'type' ],
            $params[ 'read' ],
            $params[ 'start' ],
            $params[ 'rn' ]
        );

        if( $messages === false ) {
            //$this->data[ 'messages' ] = [];
        }

        $this->data[ 'messages' ] = $messages;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $tab = intval( $request->getQuery( 'tab' ) );

        if( $tab < 1 || $tab > 4 ) {
            $tab = 1;
        }

        $start = intval( $request->getQuery( 'start' ) );

        $rn = intval( $request->getQuery( 'rn' ) );

        if( $rn <= 0 ) $rn = 20;
        if( $rn > 100 ) $rn = 100;

        $read = $request->getQuery( 'read' );

        $type = $request->getQuery( 'type' );

        $this->params = [
            'start' => $start,
            'rn' => $rn,
            'type' => $type,
            'read' => $read,
            'tab' => $tab
        ];

        return $this;
    }
}
