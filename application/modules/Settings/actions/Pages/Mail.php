<?php

Class MailAction extends \Local\BaseAction {
    private $data = [
        'page' => 'mail'
    ];

    public function __execute() {
        $this->tpl = 'settings/mail';

        if( !$this->account ) {
            $this->redirect( '/signin' );
            exit;
        }

        return $this->data;
    }

    public function __mobile() {
    }
}
