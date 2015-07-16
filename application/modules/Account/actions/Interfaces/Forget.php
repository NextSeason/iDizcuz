<?php

Class ForgetAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->checkVcode()->createToken();

        return $this->data;
    }

    private function createToken() {
        $randomStr = \Local\Utils::randomString( 8 );
        $ip = $_SERVER['REMOTE_ADDR'];
        $now = $_SERVER['REQUEST_TIME'];

        $token = sha1( $randomStr . $ip . $now );

        $this->session['forget_passwd_token'] = [
            'value' => $token,
            'time' => $now,
            'email' => $this->params['email']
        ];

        $this->data['token'] = $token;
    }

    private function checkVcode() {
        $params = $this->params;

        $vcodeConf = \Local\Utils::loadConf( 'vcode', 'forget_passwd' );

        $res = \Local\Vcode::check( $params['vcode'], $vcodeConf->lifetime, [
            'type' => 'email',
            'use' => 'forget',
            'receiver' => $params['email']
        ] );

        if( $res !== true ) {
            $this->error( $res );
        }

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $email = $request->getPost( 'email' );

        if( is_null( $email ) || !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $vcode = $request->getPost( 'vcode' );

        if( is_null( $vcode ) || strlen( $vcode ) != 6 ) {
            $this->error( 'VCODE_ERR' );
        }

        $this->params = array(
            'email' => $email,
            'vcode' => $vcode
        );

        return $this;
    }
}
