<?php

Class NotificationAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'settings/notification';

        if( !$this->account ) {
            $this->redirect( '/signin' );
            exit;
        }

        $this->data['page'] = 'notification';
        return $this->data;
    }
}
