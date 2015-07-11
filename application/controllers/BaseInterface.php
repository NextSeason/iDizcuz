<?php

Class BaseInterfaceController extends BaseController {
    public function init() {
        parent::init();

        $this->action_path = sprintf( 'modules/%s/actions/Interfaces/', $this->_module );
    }

    public function response( $data ) {
        echo json_encode( $data );
    }

    protected function setHeader() {
        $response = $this->getResponse();
        $response->setHeader( 'Content-Type', 'application/json; charset=utf-8' );
        $response->response();
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
