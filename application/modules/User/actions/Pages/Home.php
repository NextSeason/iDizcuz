<?php

Class HomeAction extends \Local\BaseAction {

    private $data = array();

    public function __execute() {
        $this->tpl = 'user/home';

        $this->paramsProcessing()->check()->getData()->reportReasons();

        $page = $this->params[ 'page' ];

        if( $page == 'removed' && !$this->account ) {
            \Local\Utils::redirect( '/signin' );
        }

        $this->data[ 'page' ] = $page;

        return $this->data;
    }

    private function reportReasons() {
        $reportConf = \Local\Utils::loadConf( 'report', 'reasons' );
        $this->data[ 'reportReasons' ] = $reportConf;
        return $this;
    }

    private function getData() {

        $id = $this->params[ 'id' ];

        $user = $this->accountModel->get( $id );

        if( !$user ) {
            $this->data[ 'user' ] = null;
        }

        $accountDataModel = new AccountDataModel();
        $accountInfoModel = new AccountInfoModel();

        $user[ 'data' ] = $accountDataModel->get( $id );
        $user[ 'info' ] = $accountInfoModel->get( $id );


        $industry = $user[ 'info' ][ 'industry' ];

        if( $industry != 0 ) {
            $industries = \Local\Utils::loadConf( 'industries', 'list' );
            $user[ 'info' ][ 'industry' ] = trim( $industries[ $industry ], '-' );
        }

        $this->data[ 'user' ] = $user;

        return $this;

    }

    private function check() {
        $id = $this->params[ 'id' ];

        $account = $this->account;

        if( !$account && !$id ) {
            \Local\Utils::redirect( '/signin' );
        }

        $this->data[ 'self' ] = 0;

        if( ( !is_null( $account ) && !$id ) || ( !is_null( $account ) && $account['id'] == $id ) ) {
            $id = $account[ 'id' ];
            $this->data[ 'self' ] = 1;
        }

        $this->params[ 'id' ] = $id;
        return $this;
    }

    private function paramsProcessing() {
        $id = $this->request->getQuery( 'u' );

        if( !is_null( $id ) && !preg_match( '#^\d+$#', $id ) ) {
            $id = null;
        }

        $page = $this->request->getQuery( 'page' );

        if( is_null( $page ) ) {
            $page = 'posts';
        }

        $this->params = array(
            'id' => $id,
            'page' => $page
        );

        return $this;
    }
}
