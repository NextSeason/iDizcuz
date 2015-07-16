<?php

Class ForgetResetAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';
        $this->paramsProcessing()->check()->updatePasswd();
        return $this->data;
    }

    private function updatePasswd() {
        $accountModel = $this->accountModel ? $this->accountModel : new AccountModel();

        $salt = md5( \Local\Utils::randomString( 20 ));

        $passwd = \Local\Utils::passwd( $this->params['passwd'], $salt );

        $res = $accountModel->updatePasswd( [
            'email' => $this->params['email'],
            'passwd' => $passwd,
            'salt' => $salt
        ] );

        if( !$res ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->session['forget_passwd_token'] = null;

        return $this;
    }

    private function check() {

        if( !isset( $this->session[ 'forget_passwd_token' ] ) ) {
            $this->redirect( 'SYSTEM_ERR' );
        }

        $token = $this->session[ 'forget_passwd_token' ];

        if( !is_array( $token ) ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $time = $token['time'];

        if( $_SERVER['REQUEST_TIME'] - $time > 120 ) {
            $this->error( 'LINK_INVALID' );
        }

        if( $token['email'] != $this->params['email'] ) {
            $this->error( 'PARAMS_ERR' );
        }

        if( $token['value'] != $this->params['token'] ) {
            $this->error( 'PARAMS_ERR' );
        }

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $email = $request->getPost( 'email' );
        if( is_null( $email ) || !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            $this->error( 'PARAMS_ERR' );
        } 

        $token = $request->getPost( 'token' );

        if( is_null( $token ) || !strlen( $token ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $passwd = $request->getPost( 'passwd' );

        if( is_null( $passwd ) || strlen( $passwd ) > 20 || strlen( $passwd ) < 6 ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = [
            'email' => $email,
            'passwd' => $passwd,
            'token' => $token
        ];

        return $this;
    }
}
