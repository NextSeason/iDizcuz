<?php

Class MessagesAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'message/messages';

        if( !$this->account ) {
            $this->redirect( '/signin' );
            exit;
        }
        $this->clearUnread();

        return $this->data;
    }

    public function __mobile() {
        $this->tpl = 'messageMobile/messages';
        if( $this->account ) {
            $this->clearUnread();
            return $this->data;
        }
    }

    private function clearUnread() {
        $accountDataModel = new AccountDataModel();

        $accountDataModel->update( [
            'set' => [ 'unread_msg' => 0 ],
            'where' => [ [ 'id', $this->account['id'] ] ]
        ] );

        return $this;
    }
}
