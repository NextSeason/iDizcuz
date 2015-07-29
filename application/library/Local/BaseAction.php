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
    protected $pool = array();

    protected $accountModel;
    protected $accountDataModel;

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
            if( empty( $data['account'] ) ) {
                $data[ 'account' ] = $this->session[ 'account' ];
            }
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
            $industries = \Local\Utils::loadConf( 'industries', 'list' );

            if( $account['industry'] != 0 ) {
                $account['industry_name'] = trim( $industries[$account['industry']], '-' );
            }

            $this->account = array_merge( $this->account, $account );
            $this->accountDataModel = new \AccountDataModel();
            $this->account['data'] = $this->accountDataModel->get( $account['id'] );


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

    protected function record( $params ) {
        if( !$this->account ) {
            return $this;
        }

        $activityModel = new \ActivityModel();

        $activityModel->insert( [
            'type' => $params[ 'type' ],
            'account_id' => $this->account['id'],
            'relation_id' => $params['relation_id']
        ] );

        return $this;
    }

    abstract protected function __execute();
}
