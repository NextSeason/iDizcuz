<?php

Class ForgetAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'account/forget';
        return $this->data;
    }

    public function __mobile() {
        $this->tpl = 'accountMobile/forget';
        return $this->data;
    }
}
