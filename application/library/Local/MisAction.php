<?php

namespace Local;

Abstract Class MisAction extends \Local\BaseAction {
    protected $adminModel;

    public function execute() {

        set_exception_handler( [ $this, 'exception_handler' ] );

        $this->session = \Yaf\Session::getInstance();
        $this->request = $this->getRequest();
        $this->controller = $this->getController();

        $this->admin = isset( $this->session [ 'admin' ] ) ? $this->session['admin'] : null;

        $this->decodeParams();

        if( is_null( $this->admin ) 
            && ( !preg_match( '#^/mis/page/signin#', $_SERVER['REQUEST_URI'] ) )
            && ( !preg_match( '#^/mis/interface/signin#', $_SERVER['REQUEST_URI'] ) )
        ) {
            $this->redirect( '/mis/page/signin' );
            exit;
        }

        if( $this->admin ) {
            $this->updateSession();
        }

        $data = $this->__execute();

        if( $this->type == 'interface' ) {
            $this->controller->success( Utils::traverseEncodeId( $data ) );
        } else {
            $data[ 'admin' ] = $this->session[ 'admin' ];
            $this->display( $this->tpl, Utils::traverseEncodeId( $data ) );
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

    protected function __mobile() {
    }
}
