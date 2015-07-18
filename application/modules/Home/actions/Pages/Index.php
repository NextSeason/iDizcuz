<?php

Class IndexAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'home/index';

        return $this->data;
    }
}
