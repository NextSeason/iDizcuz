<?php

Class BannedUsersAction extends \Local\MisAction {
    private $data = [];
    protected $tpl = 'mis/bannedUsers';

    public function __execute() {
        $this->paramsProcessing();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function paramsProcessing() {
        return $this;
    }
}
