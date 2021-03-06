<?php

Class UpdateInfoAction extends \Local\BaseAction {
    private $data = array();
    protected $type = 'interface';

    public function __execute() {

        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->updateData();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function updateData() {
        $params = $this->params;

        $accountModel = new AccountModel();

        $res = $accountModel->update( [
            'set' => [
                'sex' => $params[ 'sex' ],
                'industry' => $params[ 'industry' ],
                'employment' => $params[ 'employment' ],
                'position' => $params[ 'position' ],
                'desc' => $params[ 'desc' ],
                'birth' => $params['birth']
            ],
            'where' => [
                [ 'id', $this->account['id'] ]
            ]
        ] );

        if( !$res ) {
            $this->error( 'SYSTEM_ERR' );
        }

        return $this;
    }

    private function paramsProcessing() {
        $sex = $this->__getPost( 'sex' );

        if( is_null( $sex ) || !in_array( $sex, [ 0, 1, 2 ] ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $desc = $this->__getPost( 'desc' );

        if( is_null( $desc ) ) {
            $desc = '';
        }

        $employment = $this->__getPost( 'employment' );

        if( is_null( $employment ) ) {
            $employment = '';
        }

        $position = $this->__getPost( 'position' );

        if( is_null( $position ) ) {
            $position = '';
        }

        $industry = $this->__getPost( 'industry' );

        if( is_null( $industry ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        if( $industry != 0 ) {
            $industries = \Local\Utils::loadConf( 'industries', 'list' );

            if( isset( $industries[ $industry ] ) ) {
                $this->error( 'PARAMS_ERR', 'industries' );
            }
        }

        $birth = $this->__getPost( 'birth' );

        if( is_null( $birth ) || !strtotime( $birth ) ) {
            $birth = '0000-00-00';
        }

        $this->params = array(
            'sex' => $sex,
            'desc' => $desc,
            'employment' => $employment,
            'position' => $position,
            'industry' => $industry,
            'birth' => $birth
        );

        return $this;
    }
}

