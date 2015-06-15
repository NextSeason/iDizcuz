<?php

Class SigninAction extends \Yaf\Action_Abstract {
    private $request;
    private $params;

    private $controller;
    private $session;
    private $account;

    private $accountModel;

    public function execute() {
        $this->session = \Yaf\Session::getInstance();

        $this->request = $this->getRequest();

        $this->controller = $this->getController();

        $this->paramsProcessing()->checkStatus();

        $this->accountModel = new AccountModel();

        $this->authentication()->setSession()->updateAccountData();

        $this->controller->success();
    }

    private function authentication() {
        $params = $this->params;
        $account = $this->accountModel->getAccountByEmail( $params[ 'email' ] );

        if( !$account ) {
            $this->controller->error( 'ACCOUNT_NOTEXISTS' );
        }

        $passwd = \Local\Utils::passwd( $params[ 'passwd' ],  $account[ 'salt' ] );

        if( $passwd != $account[ 'passwd' ] ) {
            $this->controller->error( 'PASSWD_ERR' );
        }

        $account[ 'signin_time' ] = $_SERVER[ 'REQUEST_TIME' ];
        $account[ 'signin_ip' ] = $_SERVER[ 'REMOTE_ADDR' ];

        $this->account = $account;

        return $this;
    }

    private function updateAccountData() {
    }

    private function setSession() {
        $this->session[ 'account' ] = $this->account;
        return $this;
    }

    private function checkStatus() {
        $this->session[ 'account' ] = null;
    }

    private function paramsProcessing() {
        $request = $this->request;

        // get email from post data
        $email = $request->getPost( 'email' );

        /**
         * check if email is validate before check it from database
         */
        if( is_null( $email ) || !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            $this->controller->error( 'PARAMS_ERR' );
        }

        // get password
        $passwd = $request->getPost( 'passwd' );

        // check password is exists
        if( is_null( $passwd ) ) {
            $this->controller->error( 'PARAMS_ERR' );
        }

        $len = strlen( $passwd );

        // bad password
        if( $len < 6 || $len > 20 ) {
            $this->controller->error( 'PARAMS_ERR' );
        }

        $this->params = array(
            'email' => $email,
            'passwd' => $passwd
        );

        return $this;
    }
}
