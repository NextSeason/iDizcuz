<?php

Class SigninAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';
        $this->paramsProcessing()->checkStatus();

        $this->authentication()->setSession()->remember()->updateAccountData();

        return $this->data;
    }

    private function authentication() {
        $accountModel = new AccountModel();
        $params = $this->params;
        $account = $accountModel->getAccountByEmail( $params[ 'email' ] );

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

    private function remember() {
        if( $this->params['remember'] != 1 ) {
            return $this;
        }

        $remember_str = implode( '#', [
            $this->params['email'],
            $_SERVER['REQUEST_TIME'],
            \Local\Utils::randomString( 8 )
        ] );

        setCookie( 'ID-TOKEN', base64_encode( $remember_str ), $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7, '/' );

        $remember_token = md5( $remember_str ); 

        $this->pool['remember_token'] = $remember_token;

        return $this;
    }

    private function updateAccountData() {
        $accountModel = new AccountModel();

        $params = [
            'login_ip' => ip2long( $_SERVER['REMOTE_ADDR'] ),
            'mtime' => date( 'Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] )
        ];

        $params['remember_token'] = isset( $this->pool['remember_token'] ) ? $this->pool['remember_token'] : '';

        $accountModel->update( $this->account['id'], $params );

        return $this;
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

        $remember = intval( $request->getPost( 'remember' ) );

        $this->params = array(
            'email' => $email,
            'passwd' => $passwd,
            'remember' => $remember
        );

        return $this;
    }
}
