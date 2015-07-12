<?php

Class MessageAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'message/message';

        $this->paramsProcessing();

        return $this->data;
    }

    private function paramsProcessing() {
        return $this;
    }
}
