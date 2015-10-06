<?php

Class GetMessagesAction extends \Local\BaseAction {
    private $data = [];
    protected $type = 'interface';

    public function __execute() {

        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->getMessages()->getFrom();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function getFrom() {
        if( count( $this->data['messages'] ) == 0 ) {
            return $this;
        }

        $accountModel = new AccountModel();

        foreach( $this->data['messages'] as &$message ) {
            $message[ 'from' ] = $accountModel->get( $message['from'], [ 'id', 'uname' ] );
        }

        return $this;
    }

    private function getMessages() {
        $params = $this->params;

        $messageModel = new MessageModel();

        $where = [
            [ 'to', $this->account['id'] ]
        ];

        if( $params['cursor'] != 0 ) {
            $where[] = [ 'id', '<', $params['cursor'] ];
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
        $cursor = intval( $this->__getQuery( 'cursor' ) );

        $rn = intval( $this->__getQuery( 'rn' ) );

        if( $rn <= 0 ) $rn = 20;
        if( $rn > 100 ) $rn = 100;

        $type = intval( $this->__getQuery( 'type' ) );

        $this->params = [
            'cursor' => $cursor == 0 ? 0 : \Local\Utils::decodeId( $cursor ),
            'rn' => $rn,
            'type' => $type,
        ];

        return $this;
    }
}
