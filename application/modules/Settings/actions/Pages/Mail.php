<?php

Class MailAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'settings/mail';

        if( !$this->account ) {
            \Local\Utils::redirect( '/signin' );
        }

        $this->data['page'] = 'mail';
        return $this->data;
    }
}
