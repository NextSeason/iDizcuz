<?php

Class HomeAction extends \Local\BaseAction {

    private $data = array();

    public function __execute() {
        $this->tpl = 'user/home';
        return $this->data;
    }
}
