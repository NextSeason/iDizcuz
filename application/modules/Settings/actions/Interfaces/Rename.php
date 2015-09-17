<?php

Class RenameAction extends \Local\BaseAction {
    private $data = [];
    protected $type = 'interface';

    public function __execute() {
        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProgressing()->check()->insertRecord();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function insertRecord() {
        $accountRenameModel = new AccountRenameModel();
        $accountRenameModel->insert( [
            'account_id' => $this->account['id'],
            'old_uname' => $this->account['uname'],
            'new_uname' => $this->params['uname']
        ] );
        return $this;
    }

    private function check() {
        $renameable = \Accounts\RenameRecord::renameable( $this->account['id'] );
        if( !$renameable['renameable'] ) {
            $this->error( 'SYSTEM_ERR' );
        }
        return $this;
    }

    private function paramsProgressing() {
        $uname = trim( $this->__getPost( 'uname' ) );

        if( !\Local\Validate::uname( $uname ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = [
            'uname' => $uname
        ];
        return $this;
    }
}
