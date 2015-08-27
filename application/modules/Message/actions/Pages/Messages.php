<?php

Class MessagesAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'message/messages';

        if( !$this->account ) {
            $this->redirect( '/signin' );
            exit;
        }

        $this->paramsProcessing();

        $this->data[ 'tab' ] = $this->params[ 'tab' ];
        $this->data['type'] = $this->params['type'];

        return $this->data;
    }

    public function __mobile() {
        $this->tpl = 'messageMobile/messages';
        if( $this->account ) {
            return $this->data;
        }
    }

    private function paramsProcessing() {
        $tab = intval( $this->__getQuery( 'tab' ) );

        if( $tab < 0 || $tab > 4 ) {
            $tab = 0;
        }

        $type = intval( $this->__getQuery( 'type' ) );

        $this->params = [
            'type' => $type,
            'tab' => $tab
        ];

        return $this;
    }
}
