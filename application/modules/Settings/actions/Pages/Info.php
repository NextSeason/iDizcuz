<?php

Class InfoAction extends \Local\BaseAction {

    private $data = [];

    public function __execute() {
        $this->tpl = 'settings/info';

        if( !$this->account ) {
            $this->redirect( '/signin' );
            exit;

        }

        $this->getIndustries();

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
}
