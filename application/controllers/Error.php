<?php

Class ErrorController extends BaseController{
    public function errorAction() {
        $this->setViewpath( APP_PATH . '/application/views/page' );

        $data = [];

        if( $this->session['account'] ) {
            $data['account'] = $this->session['account'];
        }
        $this->display( 'error', $data );
    }
}
