<?php

Class SigninAction extends \Local\MisAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->session['admin'] = null;
        $this->paramsProcessing()->authentication()->setSession();

        return $this->data;
    }
    private function setSession() {
        $this->session['admin'] = $this->admin;
        return $this;
    }

    private function authentication() {
        $params = $this->params;

        $adminModel = new AdminModel();

        $admin = $adminModel->select( [
            'where' => [ [ 'email', $params[ 'email' ] ] ]
        ] ); 

        if( !$admin ) {
            $this->error( 'ACCOUNT_NOTEXISTS' );
        }

        $admin = $admin[0];

        $passwd = \Local\Utils::passwd( $params[ 'passwd' ], $admin[ 'salt' ] );

        if( $passwd != $admin[ 'passwd' ] ) {
            $this->error( 'PASSWD_ERR' );
        }

        $admin['signin_time' ] = $_SERVER[ 'REQUEST_TIME' ];
        $admin[ 'signin_ip' ] = $_SERVER[ 'REMOTE_ADDR' ];

        $this->admin = $admin;

        return $this;
    }

    private function paramsProcessing() {
        $email = $this->__getPost( 'email' );
        $passwd = $this->__getPost( 'passwd' );

        if( is_null( $email ) || is_null( $passwd ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = [
            'email' => $email,
            'passwd' => $passwd
        ];

        return $this;
    }
}
