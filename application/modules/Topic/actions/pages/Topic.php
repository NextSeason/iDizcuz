<?php

Class TopicAction extends \Local\BaseAction {

    private $data = array();

    public function __execute() {

        $this->tpl = 'topic/topic';

        return $this->data;
    }
}
