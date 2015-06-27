<?php

Class NewEventAction extends \Local\MisAction {
    private $data = [];

    public function __execute() {

        $this->tpl = 'mis/newEvent';

        return $this->data;
    }
}
