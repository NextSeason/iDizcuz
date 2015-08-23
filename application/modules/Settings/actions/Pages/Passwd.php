<?php

Class PasswdAction extends \Local\BaseAction {
    private $data = [
        'page' => 'passwd'
    ];

    public function __execute() {
        $this->tpl = 'settings/passwd';

        if( !$this->account ) {
            $this->redirect( '/signin' );
            exit;
        }

        $this->data['page'] = 'passwd';

        return $this->data;
    }

    public function __mobile() {
        $this->tpl = 'settingsMobile/passwd';
        return $this->data;
    }
}
