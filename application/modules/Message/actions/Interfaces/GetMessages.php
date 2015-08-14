<?php

Class GetMessagesAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->getMessages();
        return $this->data;
    }

    public function __mobile() {
    }

    private function getMessages() {
        $params = $this->params;

        $messageModel = new MessageModel();

        $where = [
            [ 'from', 0 ],
            [ 'to', $this->account['id'] ],
            [ '-del', 'IN', '(0,1)' ]
        ];

        if( $params['cursor'] != 0 ) {
            $where[] = [ 'id', '<', $params['cursor'] ];
        }

        if( $params[ 'type' ] != 0 ) {
            $where[] = [ 'type', $params[ 'type' ] ];
        }

        if( !is_null( $params[ 'read' ] ) ) {
            $where[] = [ 'read', $params['read'] ];
        }

        $messages = $messageModel->select( [
            'where' => $where,
            'order' => [ [ 'id', 'DESC' ] ],
            'rn' => $params['rn']
        ] );

        if( $messages === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data[ 'messages' ] = $messages;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;


        $cursor = intval( $request->getQuery( 'cursor' ) );

        $rn = intval( $request->getQuery( 'rn' ) );

        if( $rn <= 0 ) $rn = 20;
        if( $rn > 100 ) $rn = 100;

        $read = $request->getQuery( 'read' );

        $type = intval( $request->getQuery( 'type' ) );

        $this->params = [
            'cursor' => $cursor,
            'rn' => $rn,
            'type' => $type,
            'read' => $read
        ];

        return $this;
    }
}
