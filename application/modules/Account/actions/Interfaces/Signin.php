<?php

Class SigninAction extends \Local\BaseAction {
    private $account;

    private $data = array();

    public function __execute() {

        $this->type = 'interface';

        $this->paramsProcessing()->checkStatus();

        if( empty( $this->accountModel ) ) {
            $this->accountModel = new AccountModel();
        }

        $this->authentication()->setSession()->updateAccountData();

        return $this->data;
    }

    private function authentication() {
        $params = $this->params;
        $account = $this->accountModel->getAccountByEmail( $params[ 'email' ] );

        if( !$account ) {
            $this->error( 'ACCOUNT_NOTEXISTS' );
        }

        $passwd = \Local\Utils::passwd( $params[ 'passwd' ],  $account[ 'salt' ] );

        if( $passwd != $account[ 'passwd' ] ) {
            $this->error( 'PASSWD_ERR' );
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
            $this->error( 'PARAMS_ERR' );
        }

        // get password
        $passwd = $request->getPost( 'passwd' );

        // check password is exists
        if( is_null( $passwd ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $len = strlen( $passwd );

        // bad password
        if( $len < 6 || $len > 20 ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = array(
            'email' => $email,
            'passwd' => $passwd
        );

        return $this;
    }
}
