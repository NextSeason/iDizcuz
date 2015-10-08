<?php

Class ErrorController extends BaseController{
    public function errorAction() {
        $this->setViewpath( APP_PATH . '/application/views/page' );

        $data = [];

        $data['account'] = $this->session['account'] ? $this->session['account'] : null;

        $detect = new \Local\MobileDetect();

        if( $detect->isMobile() && !$detect->isTablet() ) {
            $this->display( '../errorMobile/error', $data );
        } else {
            $this->display( 'error', $data );
        }
    }
}
