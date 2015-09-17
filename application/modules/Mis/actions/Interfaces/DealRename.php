<?php

Class DealRenameAction extends \Local\MisAction {
    private $data = [];
    protected $type = 'interface';

    public function __execute() {
        $this->paramsProcessing()->getRenameRecord()->action();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function action() {
        $action = $this->params['action'];

        if( $action == 1 ) {
            $this->pass();
        } else {
            $this->nopass();
        }

        return $this;
    }

    private function pass() {
        $res = \Accounts\Api::rename( 
            $this->data['renameRecord']['account_id'],
            $this->data['renameRecord']['new_uname']
        );

        if( !$res ) $this->error( 'SYSTEM_ERR' );

        if( !\Accounts\RenameRecord::pass( $this->params['record_id'] ) ) {
            $this->error( 'SYSTEM_ERR' );
        }
    }

    private function nopass() {
        if( !\Accounts\RenameRecord::nopass( $this->params['record_id'] ) ) {
            $this->error( 'SYSTEM_ERR' );
        }
    }

    private function getRenameRecord() {
        $renameRecord = \Accounts\RenameRecord::getRenameRecord( $this->params['record_id'] );
        if( !$renameRecord ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->data[ 'renameRecord' ] = $renameRecord;
        return $this;
    }

    private function paramsProcessing() {
        $record_id = $this->__getPost( 'record_id' );

        if( is_null( $record_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $action = intval( $this->__getPost( 'action' ) );

        $this->params = [
            'action' => $action,
            'record_id' => $record_id
        ];

        return $this;
    }
}
