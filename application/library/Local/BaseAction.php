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

    public $__posts = [];
    public $__queries = [];
    public $__params = [];

    public function execute() {

        set_exception_handler( [ $this, 'exception_handler' ] );

        $this->session = \Yaf\Session::getInstance();
        $this->request = $this->getRequest();
        $this->controller = $this->getController();

        $this->account = isset( $this->session[ 'account' ] ) ? $this->session[ 'account' ] : null;

        if( $this->account ) {
            $this->updateSession();
        } else {
            $this->checkRemember();
        }

        $this->decodeParams();

        $detect = new \Local\MobileDetect();

        if( $detect->isMobile() && !$detect->isTablet() ) {
            $data = $this->__mobile();
        } else {
            $data = $this->__execute();
        }

        if( $this->type == 'interface' ) {
            $this->controller->success( Utils::traverseEncodeId( $data ) );
        } else {
            if( empty( $data['account'] ) ) {
                $data[ 'account' ] = $this->session[ 'account' ];
            }
            $this->display( $this->tpl, Utils::traverseEncodeId( $data ) );
        }
    }

    protected function decodeParams() {
        $this->__queries = Utils::traverseDecodeId( $this->request->getQuery() );
        $this->__posts = Utils::traverseDecodeId( $this->request->getPost() );
        $this->__params = Utils::traverseDecodeId( $this->request->getParams() );
    }

    public function __getPost( $key ) {
        return isset( $this->__posts[ $key ] ) ? $this->__posts[ $key ] : null;
    }

    public function __getParam( $key ) {
        return isset( $this->__params[ $key ] ) ? $this->__params[ $key ] : null;
    }

    public function __getQuery( $key ) {
        return isset( $this->__queries[ $key ] ) ? $this->__queries[ $key ] : null;
    }

    protected function checkRemember() {
        if( !isset( $_COOKIE['ID-TOKEN'] ) ) {
            return false;
        }
        $remember_str = base64_decode( $_COOKIE['ID-TOKEN'] );

        $remember_arr = explode( '#', $remember_str );

        if( count( $remember_arr ) != 3 ) {
            return false;
        }

        $email = $remember_arr[0];
        $time = $remember_arr[1];
        $randString = $remember_arr[2];

        if( $_SERVER['REQUEST_TIME'] - $time > 3600 * 24 * 7 ) {
            return false;
        }

        $accountModel = new \AccountModel();

        $account = $accountModel->getAccountByEmail( $email );

        if( !$account ) return false;

        if( md5( $remember_str ) != $account['remember_token'] ) {
            return false;
        }

        $industries = \Local\Utils::loadConf( 'industries', 'list' );

        if( $account['industry'] != 0 ) {
            $account['industry_name'] = trim( $industries[$account['industry']], '-' );
        }

        $accountDataModel = new \AccountDataModel();
        $account['data'] = $accountDataModel->get( $account['id'] );


        $this->account = $account;

        $this->session['account'] = $account;

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
            $ban_start = $account['ban_start'];
            $ban_end = $account['ban_end'];

            if( $ban_start == '0000-00-00' || $ban_end == '0000-00-00' ) {
                if( $account['status'] == 1 ) {
                    $account['status'] = 0;
                }
            }

            $account['mtime'] = date( 'Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] );
            $account['login_ip'] = ip2long( $_SERVER['REMOTE_ADDR'] );

            $this->accountModel->update( [
                'set' => [
                    'mtime' => $account['mtime'],
                    'login_ip' => $account['login_ip']
                ],
                'where' => [
                    [ 'id', $account['id'] ]
                ]
            ] );

            $industries = \Local\Utils::loadConf( 'industries', 'list' );

            if( $account['industry'] != 0 ) {
                $account['industry_name'] = trim( $industries[$account['industry']], '-' );
            }

            $this->account = array_merge( $this->account, $account );
            $accountDataModel = new \AccountDataModel();
            $this->account['data'] = $accountDataModel->get( $account['id'] );


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

    public function exception_handler( $exception ) {
        
    }

    abstract protected function __execute();
    abstract protected function __mobile();
}
