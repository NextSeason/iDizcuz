<?php

Class BaseController extends \Yaf\Controller_Abstract {

    protected $request;
    protected $conf;

    protected $session;

    const TOKEN_COOKIE_NAME = '__ct';

    public function init() {
        $this->request = $this->getRequest();

        /**
         * set default header for whole application
         */
        $this->setHeader();

        $this->conf = \Local\Utils::loadConf( 'site', 'product' );
        $this->session = \Yaf\Session::getInstance();
        $this->setToken();

        if( $this->request->isPost() && $this->conf->csrf_token ) {
            if( !$this->checkToken() ) {
                $this->error( 'PARAMS_ERR' );
            }
        }
    }

    /**
     * set default header for whole site
     */
    protected function setHeader() {
        $response = $this->getResponse();
        $response->setHeader( 'Content-Type', 'application/json; charset=utf-8' );
        $response->response();
    }

    /**
     * @method setToken
     * @desc set or update csrf token
     */
    private function setToken() {
        $now = $_SERVER[ 'REQUEST_TIME' ];
        /**
         * add an new token
         */
        $tokenConf = \Local\Utils::loadConf( 'csrf', 'token' )[ 'token' ];

        $newToken = md5( \Local\Utils::randomString( 24 ) . microtime() );

        $csrfTokens = empty( $this->session[ 'csrf_token' ] ) ? array() : $this->session[ 'csrf_token' ];

        /**
         * remove items out of date in csrf_token
         * remove items in csrf_token which has already reach the limit use times
         */
        foreach( $csrfTokens as $key => $token ) {
            if( $now  - $token[ 'time' ] > $tokenConf->lifetime || $token[ 'times' ] >= $tokenConf->times ) {
                $csrfTokens[ $key ] = null;
                unset( $csrfTokens[ $key ] );
            }
        }

        /**
         * add new csrf_token into session
         */
        $csrfTokens[ $newToken ] = array(
            'time' => $now,
            'times' => 0
        );

        $this->session[ 'csrf_token' ] = $csrfTokens;

        /**
         * set new cookie
         */
        echo '..setcookie';
        setcookie( self::TOKEN_COOKIE_NAME, $newToken, 0, '/' );//, $this->conf->root_domain, false, false );
    }

    /**
     * check csrf token
     *
     */
    protected function checkToken() {

        if( empty( $_COOKIE[ self::TOKEN_COOKIE_NAME ] ) ) return false;
        $token = $_COOKIE[ self::TOKEN_COOKIE_NAME ];

        $csrfTokens = $this->session[ 'csrf_token' ];
        if( empty( $csrfTokens[ $token ] ) ) return false;

        $csrfTokens[ $token ][ 'times' ]++;

        $this->session[ 'csrf_token' ] = $csrfTokens;

        return true;
    }

    /**
     * check user status
     */
    protected function loginStatus() {
        $session = $this->session;
    }

    protected function response( $data ) {
        echo json_encode( $data );
    }

    public function error( $err, $errmsg = null, $data = null ) {
        $error = \Local\Error::$$err;

        if( !is_null( $errmsg ) ) {
            $error[ 'errmsg' ] = $errmsg;
        }

        if( !is_null( $data ) ) {
            $error[ 'data' ] = $data;
        }

        $this->response ( $error );
        exit;
    }

    public function success( $data = null ) {
        $this->response( array(
            'errno' => 0,
            'data' => $data
        ) );
    }
}
