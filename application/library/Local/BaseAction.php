<?php

namespace Local;

Abstract Class BaseAction extends \Yaf\Action_Abstract {

    protected $session;
    protected $controller;
    protected $request;
    protected $tpl;
    protected $type = 'page';
    protected $account = null;
    protected $params = array();

    protected $accountModel;

    public function execute() {
        $this->session = \Yaf\Session::getInstance();
        $this->request = $this->getRequest();
        $this->controller = $this->getController();

        $this->account = isset( $this->session[ 'account' ] ) ? $this->session[ 'account' ] : null;

        if( $this->account ) {
            $this->updateSession();
        }

        $data = $this->__execute();

        if( $this->type == 'interface' ) {
            $this->controller->success( $data );
        } else {
            $data[ 'account' ] = $this->session[ 'account' ];
            $this->display( $this->tpl, $data );
        }
    }

    protected function success( $response = null ) {
        $this->controller->success( $response );
    }

    protected function error( $err, $errmsg = null, $data = null ) {
        $this->controller->error( $err, $errmsg, $data );
    }

    protected function currentAccount() {
        return $this->session[ 'account' ];
    }

    protected function updateSession() {
        $this->accountModel = new \AccountModel();

        $account = $this->accountModel->get( $this->account[ 'id' ]  );

        if( $account ) {
            $this->account = array_merge( $this->account, $account );
        } else {
            $this->account = null;
        }

        $this->session[ 'account' ] = $this->account;

        return $this;
    }

    protected function accountStatus( $id ) {
        $account = $this->session[ 'account' ];

        if( $account && $account[ 'id' ] == $id ) {
            return $account[ 'status' ];
        }

        if( empty( $this->accountModel ) ) {
            $this->accountModel = new \AccountModel();
        }
    }

    abstract protected function __execute();
}
