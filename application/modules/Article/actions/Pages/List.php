<?php

Class ListAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'article/list';

        return $this->data;
    }

}
