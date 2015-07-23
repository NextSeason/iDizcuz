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

        $router->addRoute( 'articles', new \Yaf\Route\Rewrite( '/articles/:id', [
            'module' => 'article',
            'controller' => 'page',
            'action' => 'list'
        ] ) );

        $router->addRoute( 'article', new \Yaf\Route\Regex( '#/article/(\d+)#', [
            'module' => 'article',
            'controller' => 'page',
            'action' => 'article'
        ], [
            1 => 'id'
        ] ) );

        $router->addRoute( 'topic', new \Yaf\Route\Regex( '#/topic/(\d+)#', [
            'module' => 'topic',
            'controller' => 'page',
            'action' => 'topic'
        ], [
            1 => 'id'   
        ] ) );

        $router->addRoute( 'list', new \Yaf\Route\Rewrite( '/list/:id', [
            'module' => 'topic',
            'controller' => 'page',
            'action' => 'list'
        ] ) );

        $router->addRoute( 'post', new \Yaf\Route\Rewrite( '/post/:id', [
            'module' => 'topic',
            'controller' => 'page',
            'action' => 'post'
        ] ) );

        $router->addRoute( 'user-posts', new \Yaf\Route\Rewrite( '/user/posts/:id', [
            'module' => 'user',
            'controller' => 'page',
            'action' => 'home'
        ] ) );

        $router->addRoute( 'user-activities', new \Yaf\Route\Rewrite( '/user/activities/:id', [
            'module' => 'user',
            'controller' => 'page',
            'action' => 'home'
        ] ) );

        $router->addRoute( 'user-mark', new \Yaf\Route\Rewrite( '/user/mark/:id', [
            'module' => 'user',
            'controller' => 'page',
            'action' => 'home'
        ] ) );

        $router->addRoute( 'user-follow', new \Yaf\Route\Rewrite( '/user/follow/:id', [
            'module' => 'user',
            'controller' => 'page',
            'action' => 'home'
        ] ) );

        $router->addRoute( 'user-fans', new \Yaf\Route\Rewrite( '/user/fans/:id', [
            'module' => 'user',
            'controller' => 'page',
            'action' => 'home'
        ] ) );
    }

    public function _initSession() {
        // session_start();
        \Yaf\Session::getInstance()->start();
    }
}
