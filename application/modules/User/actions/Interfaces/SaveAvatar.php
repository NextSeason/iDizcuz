<?php

Class SaveAvatarAction extends \Local\BaseAction {

    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        \Local\FileManage::saveAvatar();

        return $this->data;
    }

    private function paramsProcessing() {
        return $this;
    }
}
