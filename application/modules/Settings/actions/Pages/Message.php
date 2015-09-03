<?php

Class MessageAction extends \Local\BaseAction {
    private $data = [
        'page' => 'message'
    ];

    public function __execute() {
        $this->tpl = 'settings/message';

        if( !$this->account ) {
            $this->redirect( '/signin' );
            exit;
        }

        $this->getSettings();

        return $this->data;
    }

    public function __mobile() {
        $this->tpl = 'settingsMobile/message';
        if( $this->account ) {
            $this->getSettings();
        }
        return $this->data;
    }

    private function getSettings() {
        $accountSettingsModel = new AccountSettingsModel();

        $accountSettings = $accountSettingsModel->get( $this->account['id'] );

        $this->data[ 'settings' ] = $accountSettings;

        return $this;
    }
}
