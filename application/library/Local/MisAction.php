<?php

namespace Local;

Abstract Class MisAction extends \Yaf\Action_Abstract {
    protected $session;
    protected $controller;
    protected $request;
    protected $tpl;
    protected $type = 'page';
    protected $admin;
    protected $params = array();

    protected $adminModel;

    public function execute() {
        $this->session = \Yaf\Session::getInstance();
        $this->request = $this->getRequest();
        $this->controller = $this->getController();

        $this->admin = $this->session [ 'admin' ];

        if( $this->admin ) {
            $this->updateSession();
        }

        $data = $this->__execute();

        if( $this->type == 'interface' ) {
            $this->controller->success( $data );
        } else {
            $data[ 'admin' ] = $this->session[ 'admin' ];
            $this->display( $this->tpl, $data );
        }
    }

    protected function success( $response = null ) {
        $this->controller->success( $response );
    }

    protected function error( $err, $errmsg = null, $data = null ) {
        $this->controller->error( $err, $errmsg, $data );
    }

    protected function updateSession() {
        $this->adminModel = new \AdminModel();

        $admin = $this->adminModel->get( $this->admin[ 'id' ] );

        if( $admin ) {
            $this->admin = array_merge( $this->admin, $admin );
        }

        return $this;
    }

    protected function getPower( $id ) {
        $admin = $this->session[ 'admin' ];
        if( $admin && $admin[ 'id' ] == $id ) {
            return $admin[ 'status' ];
        }

        if( empty( $this->adminModel ) ) {
            $this->adminModel = new \AdminModel();
        }
    }
}
