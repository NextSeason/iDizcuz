<?php

Class SigninAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {

        $this->tpl = 'account/signin';

        return $this->data;
    }

    public function __mobile() {
        $this->tpl = 'accountMobile/signin';
        return $this->data;
    }
}
