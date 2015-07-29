<?php

Class UpdateInfoAction extends \Local\BaseAction {
    private $data = array();

    public function __execute() {
        $this->type = 'interface';

        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->updateData();

        return $this->data;
    }

    private function updateData() {
        $params = $this->params;

        $accountModel = new AccountModel();

        $res = $accountModel->update( $this->account[ 'id' ], array(
            'sex' => $params[ 'sex' ],
            'industry' => $params[ 'industry' ],
            'employment' => $params[ 'employment' ],
            'position' => $params[ 'position' ],
            'desc' => $params[ 'desc' ],
            'birth' => $params['birth']
        ) );

        if( !$res ) {
            $this->error( 'SYSTEM_ERR' );
        }

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $sex = $request->getPost( 'sex' );

        if( is_null( $sex ) || !in_array( $sex, [ 0, 1, 2 ] ) ) {
            $this->error( 'PARAMS_ERR', 'sex' );
        }

        $desc = $request->getPost( 'desc' );

        if( is_null( $desc ) ) {
            $desc = '';
        }

        $employment = $request->getPost( 'employment' );

        if( is_null( $employment ) ) {
            $employment = '';
        }

        $position = $request->getPost( 'position' );

        if( is_null( $position ) ) {
            $position = '';
        }

        $industry = $request->getPost( 'industry' );

        if( is_null( $industry ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        if( $industry != 0 ) {
            $industries = \Local\Utils::loadConf( 'industries', 'list' );

            if( isset( $industries[ $industry ] ) ) {
                $this->error( 'PARAMS_ERR', 'industries' );
            }
        }

        $birth = $request->getPost( 'birth' );

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

