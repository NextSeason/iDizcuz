<?php

Class VcodeAction extends \Yaf\Action_Abstract {

    private $request;
    private $params;

    private $controller;
    private $session;
    private $code;

    public function execute() {

        $this->session = \Yaf\Session::getInstance();

        $this->request = $this->getRequest();

        $this->controller = $this->getController();

        !$this->paramsProcessing()->saveCode()->sendCode();
        
        $this->controller->success();
    }

    private function saveCode() {
        /**
         * use method randomString to create a vcode filled with only number
         */
        $this->code = \Local\Utils::randomString( 6, 1 );

        $vcodes = empty( $this->session[ 'vcodes' ] ) ? array() : $this->session[ 'vcodes' ];

        /**
         * i don not think it is necessary to do check duplication
         * because it is little probability event which create two or more same vcode from randomString method.
         * and if it happens, it will not make bad experince
         */
        $vcodes[ $this->code ] = array(
            'time' => $_SERVER[ 'REQUEST_TIME' ],
            'type' => $this->params[ 'type' ],
            'use' => $this->params[ 'do' ]
        );

        $this->session[ 'vcodes' ] = $vcodes;

        return $this;
    }

    private function sendCode() {
        $params = $this->params;
        $type = $params[ 'type' ];

        if( $type == 'email' ) {
            $this->sendEmail();
            return $this;
        }

        if( $type = 'phone' ) {
            $this->sendSMS();
            return $this;
        }

        $this->controller->error( 'PARAMS_ERR' );
        return $this;
    }

    private function sendEmail() {
        $emailConf = \Local\Utils::loadConf( 'email', 'grouple' );

        $params = array(
            'From' => $emailConf->addresses->system,
            'Subject' => 'Grouple验证码',
            'Body' => $this->code
        );
        
        \Local\Send::email( $params, array( 
            array( $this->params[ 'to' ], $this->params[ 'to' ] )
        ) );
    }

    private function sendSMS() {
    }

    /**
     * merge all params in query string or $_POST, or others params together.
     */
    private function paramsProcessing() {
        $do = $this->request->getPost( 'do' );

        if( !in_array( $do, array( 'reg', 'login' ) ) ) $this->controller->error( 'PARAMS_ERR' );

        /**
         * get email address or cellphone number which user typed in
         */
        $to = $this->request->getPost( 'to' );

        if( filter_var( $to, FILTER_VALIDATE_EMAIL ) ) {
            $type = 'email';
        } else if( preg_match( '#^\d{11}$#', $to ) ) {
            $type = 'phone';
        } else {
            $this->controller->error( 'PARAMS_ERR' );
        }

        $this->params = array(
            'do' => $do,
            'to' => $to,
            'type' => $type
        );

        return $this;
    }
}
