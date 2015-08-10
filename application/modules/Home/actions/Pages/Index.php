<?php

Class IndexAction extends \Local\BaseAction {
    private $data = [
        'nav' => 'home'
    ];

    public function __execute() {
        $this->tpl = 'home/index';

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }
}
