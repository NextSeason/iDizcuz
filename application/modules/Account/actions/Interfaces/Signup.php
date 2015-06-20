<?php

Class SignupAction extends \Local\BaseAction {

    private $data = array();

    private $transactionModel;

    public function __execute() {

        $this->type = 'interface';

        $this->paramsProcessing()->checkVcode();

        if( empty( $this->accountModel ) ) {
            $this->accountModel = new AccountModel();
        }
        
        $this->checkExists();

        $this->transactionModel = new TransactionModel();
        
        $this->addAccount()->setSession();

        return $this->data;
    }

    private function setSession() {
        $this->session[ 'account' ] = $this->account;
        return $this;
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
            $this->error( 'VCODE_ERR' );
        }

        return $this;
    }

    private function checkExists() {
        $account =  $this->accountModel->getAccountByEmail( $this->params[ 'email' ] );

        if( $account ) {
            $this->error( 'ACCOUNT_EXISTS' );
        }

        return $this;
    }

    private function addAccount() {
        $params = $this->params;

        $salt = md5( \Local\Utils::randomString( 20 ) );

        $passwd = sha1( $params[ 'passwd' ] . $salt );

        $ip = $_SERVER[ 'REMOTE_ADDR' ];

        $account = array(
            'email' => $params[ 'email' ],
            'passwd' => $passwd,
            'salt' => $salt,
            'uname' => $params[ 'uname' ],
            'reg_ip' => $ip,
            'login_ip' => $ip
        );

        $account_id = $this->transactionModel->addAccount( $account );

        if( !$account_id ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $account[ 'id' ] = $account_id;

        $account[ 'signin_time' ] = $_SERVER[ 'REQUEST_TIME' ];
        $account[ 'signin_ip' ] = $_SERVER[ 'REMOTE_ADDR' ];

        $this->account = $account;

        return $this;
    }


    private function paramsProcessing() {
        $request = $this->request;

        $email = $request->getPost( 'email' );

        if( is_null( $email ) || !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $uname = $request->getPost( 'uname' );

        if( is_null( $uname ) || strlen( $uname ) > 18 ) {
            $this->error( 'PARAMS_ERR' );
        }

        $passwd = $request->getPost( 'passwd' );

        if( is_null( $passwd ) ) {
            $this->error( 'PARAMS_ERR' );
        }
        $len = strlen( $passwd );

        if( $len < 6 || $len > 20 ) {
            $this->error( 'PARAMS_ERR' );
        }

        $vcode = $request->getPost( 'vcode' );
        $len = strlen( $vcode );

        if( is_null( $vcode ) || $len != 6 ) {
            $this->error( 'PARAMS_ERR' );
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
