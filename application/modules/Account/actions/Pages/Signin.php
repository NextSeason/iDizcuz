<?php

Class SigninAction extends \Local\BaseAction {
    public function __execute() {
        $data = array();

        $this->tpl = 'account/signin';

        return $data;
    }
}
