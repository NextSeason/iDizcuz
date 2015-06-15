<?php

namespace Local;

Abstract Class BaseAction extends \Yaf\Action_Abstract {

    protected $session;
    protected $controller;
    protected $request;
    protected $tpl;
    protected $type = 'page';

    public function execute() {
        $this->session = \Yaf\Session::getInstance();
        $this->request = $this->getRequest();
        $this->controller = $this->getController();

        $data = $this->__execute();

        if( $this->type == 'interface' ) {
            $this->controller->success( $data );
        } else {
            $data[ 'account' ] = $this->session[ 'account' ];
            $this->display( $this->tpl, $data );
        }
    }

    abstract protected function __execute();

}
