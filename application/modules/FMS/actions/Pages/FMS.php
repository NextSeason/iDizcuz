<?php

Class FMSAction extends \Local\MisAction {
    private $data = [];

    public function __execute() {
        $this->paramsProgressing();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function paramsProgressing() {
        $page = $this->__getParam( 'page' );
        $this->tpl = 'fms/' . $page;
        return $this;
    }
}
