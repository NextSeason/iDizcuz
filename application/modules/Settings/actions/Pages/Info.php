<?php

Class InfoAction extends \Local\BaseAction {

    private $data = [];

    public function __execute() {
        $this->tpl = 'settings/info';

        if( !$this->account ) {
            \Local\Utils::redirect( '/signin' );
        }

        $this->accountInfo();

        return $this->data;
    }

    private function accountInfo() {

        return $this;
    }

}
