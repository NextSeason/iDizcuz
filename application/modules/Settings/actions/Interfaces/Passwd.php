<?php

Class PasswdAction extends \Local\BaseAction {
    private $data = array();
    protected $type = 'interface';

    public function __execute() {
        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->checkPasswd()->updatePasswd();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function updatePasswd() {
        $passwd = \Local\Utils::passwd( $this->params[ 'npasswd' ], $this->account[ 'salt' ] );
        $res = $this->accountModel->update( $this->account[ 'id' ], array(
            'passwd' => $passwd
        ) );

        if( !$res ) {
            $this->error( 'SYSTEM_ERR' );
        } 

        return $this;
    }

    private function checkPasswd() {
        $opasswd = $this->params[ 'opasswd' ];

        $passwd = \Local\Utils::passwd( $opasswd, $this->account[ 'salt' ] );

        if( $passwd != $this->account[ 'passwd' ] ) {
            $this->error( 'PASSWD_ERR' );
        }

        return $this;
    }



    private function paramsProcessing() {
        $request = $this->request;

        $opasswd = $request->getPost( 'opasswd' );

        if( is_null( $opasswd ) || strlen( $opasswd ) == 0 ) {
            $this->error( 'PARAMS_ERR' );
        }
        $npasswd = $request->getPost( 'npasswd' );

        if( is_null( $npasswd ) || strlen( $npasswd ) < 6 || strlen( $npasswd ) > 20 ) {
            $this->error( 'PARAMS_ERR' );
        }

        if( $opasswd == $npasswd ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = [
            'opasswd' => $opasswd,
            'npasswd' => $npasswd
        ];

        return $this;
    }
}
