<?php

Class SignoutAction extends \Local\BaseAction {

    public function __execute() {
        $this->session[ 'account' ] = null;

        if( $_SERVER[ 'HTTP_REFERER' ] == '' ) {
            $this->redirect( 'http://www.idizcuz.com' );
        }

        $this->redirect( $_SERVER['HTTP_REFERER'] );
    }
}
