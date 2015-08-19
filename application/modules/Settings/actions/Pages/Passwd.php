<?php

Class PasswdAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'settings/passwd';

        if( !$this->account ) {
            $this->redirect( '/signin' );
            exit;
        }

        $this->data['page'] = 'passwd';

        return $this->data;
    }
}
