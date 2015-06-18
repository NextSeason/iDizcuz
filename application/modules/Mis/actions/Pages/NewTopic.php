<?php

Class NewTopicAction extends \Local\MisAction {
    public function __execute() {
        $data = array();

        $this->tpl = 'mis/newTopic';

        return $data;
    }
}
