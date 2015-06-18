<?php

Class IndexAction extends \Local\MisAction {
    public function __execute() {
        $data = array();

        $this->tpl = 'mis/index';

        return $data;
    }
}
