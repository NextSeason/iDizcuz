<?php

Class SignoutAction extends \Local\BaseAction {

    public function __execute() {
        $this->session[ 'account' ] = null;

        // remove remember token cookie
        setCookie( 'ID-TOKEN', '', -1, '/' );

        if( $_SERVER[ 'HTTP_REFERER' ] == '' ) {
            $this->redirect( 'http://www.idizcuz.com' );
        }

        $this->redirect( $_SERVER['HTTP_REFERER'] );
    }

    public function __mobile() {
        return $this->__execute();
    }
}
