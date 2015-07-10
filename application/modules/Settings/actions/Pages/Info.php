<?php

Class InfoAction extends \Local\BaseAction {

    private $data = [];
    private $accountInfoModel;

    public function __execute() {
        $this->tpl = 'settings/info';

        if( !$this->account ) {
            \Local\Utils::redirect( '/signin' );
        }

        $this->accountInfoModel = new AccountInfoModel();

        $this->accountInfo()->getIndustries();

        $this->data[ 'page' ] = 'info';

        return $this->data;
    }

    private function getIndustries() {
        $industries = \Local\Utils::loadConf( 'industries', 'list' );

        if( !$industries ) {
            $industries = [];
        }

        $this->data[ 'industries' ] = $industries;
        return $this;
    }

    private function accountInfo() {
        $accountInfo = $this->accountInfoModel->get( $this->account[ 'id' ] );

        if( !$accountInfo ) {
            $accountInfo = [];
        }

        $this->data[ 'accountInfo' ] = $accountInfo; 
        return $this;
    }

}
