<?php

Class FMSAction extends \Local\MisAction {
    private $data = [];

    public function __execute() {
        $this->paramsProgressing();

        $this->tpl = 'fms/' . $this->params['page'];

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function paramsProgressing() {
        $page = $this->__getParam( 'page' );

        $this->params = [
            'page' => $page
        ];
        return $this;
    }
}
