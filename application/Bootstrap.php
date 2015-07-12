<?php

Class Bootstrap extends \Yaf\Bootstrap_Abstract {

    public function _initLoader() {
        \Yaf\Loader::getInstance()->registerLocalNameSpace( array( 'Utils', 'Send', 'PHPMailer' ) );
    }
        
    public function _initConfig() {
        \Yaf\Dispatcher::getInstance()->disableView();
    }

    public function _initPlugin() {
        // register plugins here
    }

    public function _initRoute() {
        $router = \Yaf\Dispatcher::getInstance()->getRouter();
        $router->addRoute( 'idizcuz', new \Yaf\Route\Supervar( '__r' ) );
    }

    public function _initSession() {
        // session_start();
        \Yaf\Session::getInstance()->start();
    }
}
