<?php

Class HomeAction extends \Local\BaseAction {

    private $data = [];
    private $tpls = [
        'posts' => 'user/posts',
        'mark' => 'user/mark',
        'activities' => 'user/activities',
        'follow' => 'user/follow',
        'fans' => 'user/follow'
    ];

    public function __execute() {
        $this->paramsProcessing()->check()->getData()->getFollowStatus()->reportReasons();

        $page = $this->params[ 'page' ];

        $this->tpl = $this->tpls[ $page ];

        if( $page == 'removed' && !$this->account ) {
            \Local\Utils::redirect( '/signin' );
        }

        $this->data[ 'page' ] = $page;

        return $this->data;
    }
    private function getFollowStatus() {
        if( !$this->account ) {
            $this->data['followed'] = false;
            return $this;
        }

        $followModel = new FollowModel();

        $follow = $followModel->getFollowStatus( [
            'account_id' => $this->params[ 'id' ], 
            'fans_id' => $this->account['id']
        ] );

        $this->data['followed'] = $follow ? true : false;

        return $this;
    }

    private function reportReasons() {
        $reportConf = \Local\Utils::loadConf( 'report', 'reasons' );
        $this->data[ 'reportReasons' ] = $reportConf;
        return $this;
    }

    private function getData() {

        $id = $this->params[ 'id' ];

        $accountModel = $this->accountModel ? $this->accountModel : new AccountModel();

        $user = $accountModel->get( $id );

        if( !$user ) {
            $this->data[ 'user' ] = null;
        }

        $accountDataModel = new AccountDataModel();

        $user[ 'data' ] = $accountDataModel->get( $id );

        $industry = $user[ 'industry' ];

        if( $industry != 0 ) {
            $industries = \Local\Utils::loadConf( 'industries', 'list' );
            $user[ 'industry' ] = trim( $industries[ $industry ], '-' );
        }

        $this->data[ 'user' ] = $user;

        return $this;
    }

    private function check() {
        $this->data['self'] = false;
        if( ( $this->account && $this->account['id'] == $this->params['id'] ) ) {
            $this->data[ 'self' ] = true;
        }
        return $this;
    }

    private function paramsProcessing() {
        $id = $this->request->getParam( 'id' );

        if( !is_null( $id ) && !preg_match( '#^\d+$#', $id ) ) {
            $id = null;
        }

        $uri = $_SERVER['REQUEST_URI'];

        if( preg_match( '#^/user/(\w+)/\d+$#', $uri, $matches ) ) {
            $page = $matches[1];
        }

        $this->params = array(
            'id' => $id,
            'page' => $page
        );

        return $this;
    }
}
