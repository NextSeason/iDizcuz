<?php

Class SignoutAction extends \Local\BaseAction {

    public function __execute() {
        $this->session[ 'account' ] = null;

        header( 'Location: ' . $_SERVER[ 'HTTP_REFERER' ] );
        exit;
    }
}
