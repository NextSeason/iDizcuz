<?php

Class ForgetResetAction extends \Local\BaseAction {
    private $data = [
        'error' => 0
    ];

    public function __execute() {
        $this->tpl = 'account/forgetreset';

        $this->paramsProcessing()->check();

        return $this->data;
    }

    public function __mobile() {
        $this->tpl = 'accountMobile/forgetreset';

        $this->paramsProcessing()->check();
       
        return $this->data;
    }

    private function check() {
        if( !isset( $this->session[ 'forget_passwd_token' ] ) ) {
            $this->redirect( '/' );
        }
        $token = $this->session[ 'forget_passwd_token' ];

        if( !is_array( $token ) ) {
            $this->redirect( '/' );
        }

        $time = $token['time'];

        if( $_SERVER['REQUEST_TIME'] - $time > 120 ) {
            $this->data['error'] = 1;
        } else {

            $this->data['token'] = $token['value'];
            $this->data['email'] = $token['email'];
        }
        return $this;
    }

    private function paramsProcessing() {
        $token = $this->request->getQuery( 'token' );

        if( is_null( $token ) || !strlen( $token ) ) {
            $this->redirect( '/' );
        }

        $this->params['token'] = $token;

        return $this;
    }

}
