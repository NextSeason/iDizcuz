<?php

Class SignupAction extends \Local\BaseAction {

    private $data = array();

    public function __execute() {

        $this->type = 'interface';

        $this->paramsProcessing()->checkVcode()->checkExists()->addAccount()->setSession();

        $this->record( [
            'type' => 5,
            'relation_id' => $this->pool['account_id']
        ] );
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
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

        if( $res !== true ) {
            $this->error( $res );
        }

        return $this;
    }

    private function checkExists() {
        $accountModel = $this->accountModel ? $this->accountModel : new AccountModel();

        $account =  $accountModel->getAccountByEmail( $this->params[ 'email' ] );

        if( $account ) {
            $this->error( 'ACCOUNT_EXISTS' );
        }

        return $this;
    }

    private function addAccount() {
        $params = $this->params;

        $salt = md5( \Local\Utils::randomString( 20 ) );

        $passwd = \Local\Utils::passwd( $params['passwd'], $salt );

        $ip = ip2long( $_SERVER[ 'REMOTE_ADDR' ] );

        $account = array(
            'email' => $params[ 'email' ],
            'passwd' => $passwd,
            'salt' => $salt,
            'uname' => $params[ 'uname' ],
            'reg_ip' => $ip,
            'login_ip' => $ip
        );

        $transactionModel = new TransactionModel();

        $account_id = $transactionModel->addAccount( $account );

        if( !$account_id ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->pool['account_id'] = $account_id;

        $account[ 'id' ] = $account_id;

        $account[ 'signin_time' ] = $_SERVER[ 'REQUEST_TIME' ];
        $account[ 'signin_ip' ] = $_SERVER[ 'REMOTE_ADDR' ];

        $this->account = $account;

        return $this;
    }


    private function paramsProcessing() {

        $email = $this->__getPost( 'email' );

        if( is_null( $email ) || !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $uname = $this->__getPost( 'uname' );

        if( is_null( $uname ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $uname = trim( $uname );

        if( $uname == '' || mb_strlen( $uname ) > 12 ) {
            $this->error( 'PARAMS_ERR' );
        }

        $passwd = $this->__getPost( 'passwd' );

        if( is_null( $passwd ) ) {
            $this->error( 'PARAMS_ERR' );
        }
        $len = strlen( $passwd );

        if( $len < 6 || $len > 20 ) {
            $this->error( 'PARAMS_ERR' );
        }

        $vcode = $this->__getPost( 'vcode' );

        if( is_null( $vcode ) || strlen( $vcode ) != 6 ) {
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
