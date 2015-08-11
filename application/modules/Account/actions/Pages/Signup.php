<?php

Class SignupAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'account/signup';
        return $this->data;
    }

    public function __mobile() {
        $this->tpl = 'accountMobile/signup';
        return $this->data;
    }
}
