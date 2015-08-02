<?php

Class ActivitiesAction extends \Local\BaseAction {
    private $data = [
        'nav' => 'activities'
    ];

    public function __execute() {
        $this->tpl = 'home/activities';

        return $this->data;
    }
}
