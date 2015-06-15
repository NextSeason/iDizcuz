<?php

Class SignupAction extends \Yaf\Action_Abstract {

    private $request;
    private $params;

    private $controller;
    private $session;

    private $accountModel;

    public function execute() {
        $this->session = \Yaf\Session::getInstance();

        $this->request = $this->getRequest();

        $this->controller = $this->getController();

        $this->paramsProcessing()->checkVcode();
        
        $this->accountModel = new AccountModel();
        
        $this->checkExists()->addAccount();

        $this->controller->success();
    }

    private function checkVcode() {

        $params = $this->params;

        $vcodeConf = \Local\Utils::loadConf( 'vcode', 'reg' );

        $res = \Local\Vcode::check( $params[ 'vcode' ], $vcodeConf->lifetime, array( 
            'type' => 'email',
            'use' => 'signup',
            'receiver' => $params[ 'email' ] 
        ) );

        if( !$res ) {
            $this->controller->error( 'VCODE_ERR' );
        }

        return $this;
    }

    private function checkExists() {
        $account =  $this->accountModel->getAccountByEmail( $this->params[ 'email' ] );

        if( $account ) {
            $this->controller->error( 'ACCOUNT_EXISTS' );
        }

        return $this;
        
    }

    private function addAccount() {
        $params = $this->params;

        $salt = md5( \Local\Utils::randomString( 20 ) );

        $passwd = sha1( $params[ 'passwd' ] . $salt );

        $ip = $_SERVER[ 'REMOTE_ADDR' ];

        $data = array(
            'email' => $params[ 'email' ],
            'passwd' => $passwd,
            'salt' => $salt,
            'uname' => $params[ 'uname' ],
            'reg_ip' => $ip,
            'login_ip' => $ip
        );

        $res = $this->accountModel->addAccount( $data );
    }

    private function paramsProcessing() {
        $request = $this->request;

        $email = $request->getPost( 'email' );

        if( is_null( $email ) || !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            $this->controller->error( 'PARAMS_ERR' );
        }

        $uname = $request->getPost( 'uname' );

        if( is_null( $uname ) || strlen( $uname ) > 18 ) {
            $this->controller->error( 'PARAMS_ERR' );
        }

        $passwd = $request->getPost( 'passwd' );

        if( is_null( $passwd ) ) {
            $this->controller->error( 'PARAMS_ERR' );
        }
        $len = strlen( $passwd );

        if( $len < 6 || $len > 20 ) {
            $this->controller->error( 'PARAMS_ERR' );
        }

        $vcode = $request->getPost( 'vcode' );
        $len = strlen( $vcode );

        if( is_null( $vcode ) || $len != 6 ) {
            $this->controller->error( 'PARAMS_ERR' );
        }

        $this->params = array(
            'email' => $email,
            'uname' => $uname,
            'passwd' => $passwd,
            'vcode' => $vcode
        );

        return $this;
    }

}
