<?php

Class GetMessagesAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->getMessages()->getExtraData();
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
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data[ 'messages' ] = $messages;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;


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
            'read' => $read
        ];

        return $this;
    }
}
