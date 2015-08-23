<?php

Class InfoAction extends \Local\BaseAction {

    private $data = [
        'page' => 'info'
    ];

    public function __execute() {
        $this->tpl = 'settings/info';

        if( !$this->account ) {
            $this->redirect( '/signin' );
            exit;
        }

        $this->getIndustries();

        return $this->data;
    }

    public function __mobile() {
        $this->tpl = 'settingsMobile/info';

        if( !$this->account ) {
            return $this->data;
        }

        $this->getIndustries();

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
