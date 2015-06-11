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

        $route = new \Yaf\Route\Rewrite(
            '/collection',
            array(
                'module' => 'collection',
                'controller' => 'switch',
                'action' => 'entrance'
            )
        );

        $router->addRoute( 'collection', $route );

        /*
        $route = new \Yaf\Route\Rewrite(
            '/:module/
        );
         */
    }

    public function _initSession() {
        // session_start();
        \Yaf\Session::getInstance()->start();
    }
}
