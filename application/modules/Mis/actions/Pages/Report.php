<?php

Class ReportAction extends \Local\ReportAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'mis/report';

        $this->paramsProcessing()->getReport();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }
}
