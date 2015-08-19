<?php

Class MailAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'settings/mail';

        if( !$this->account ) {
            $this->redirect( '/signin' );
            exit;
        }

        $this->data['page'] = 'mail';
        return $this->data;
    }
}
