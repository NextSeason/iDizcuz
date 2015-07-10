<?php

Class NotificationAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'settings/notification';

        if( !$this->account ) {
            \Local\Utils::redirect( '/signin' );
        }

        $this->data['page'] = 'notification';
        return $this->data;
    }
}
