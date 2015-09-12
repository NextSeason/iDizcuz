<?php

Class SendVcodeAction extends \Local\BaseAction {

    private $code;
    private $data = array();

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->checkFreq()->checkParams()->saveCode()->sendCode();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function checkParams() {
        if( $this->params['type'] == 'email' ) {
            $this->checkEmail();
        }

        return $this;
    }

    private function checkEmail() {
        $accountModel = $this->accountModel ? $this->accountModel : new AccountModel();

        $account = $accountModel->getAccountByEmail( $this->params['to'] );

        if( $account && $this->params['do'] == 'signup' ) {
            $this->error( 'ACCOUNT_EXISTS' );
        }

        if( !$account && $this->params['do'] == 'forget' ) {
            $this->error( 'ACCOUNT_NOTEXISTS' );
        }
    }

    private function checkFreq() {
        $codes = \Local\Vcode::get( [
            'type' => $this->params['type'],
            'use' => $this->params['do']
        ] );

        if( !count( $codes ) ) return $this;

        $now = $_SERVER[ 'REQUEST_TIME' ];

        foreach( $codes as $code ) {
            if( $now - $code['time'] < 60 ) {
                $this->error( 'FREQ_TOOHIGH' );
            }
        }
        return $this;
    }

    private function saveCode() {
        $params = $this->params;
        /**
         * use method randomString to create a vcode filled with only number
         */
        $this->code = \Local\Utils::randomString( 6, 1 );

        \Local\Vcode::save( $this->code, array( 
            'type' => $params[ 'type' ], 
            'use' => $params[ 'do' ],
            'receiver' => $params[ 'to' ]
        ) );

        return $this;
    }

    private function sendCode() {
        $params = $this->params;
        $type = $params[ 'type' ];

        if( $type == 'email' ) {
            $this->sendEmail();
            return $this;
        }

        if( $type = 'phone' ) {
            $this->sendSMS();
            return $this;
        }

        $this->error( 'PARAMS_ERR' );
        return $this;
    }

    private function sendEmail() {
        $to = $this->params[ 'to' ];

        $conf = \Local\Utils::loadConf( 'email', $this->params['do'] );

        $params = array(
            'Subject' => $conf->subject,
            'Body' => $this->getView()->render( $conf->body, array(
                'code' => $this->code
            ) )
        );
        
        \Local\Send::email( $params, array( 
            array( $to, $to )
        ) );
    }

    private function sendSMS() {
    }

    /**
     * merge all params in query string or $_POST, or others params together.
     */
    private function paramsProcessing() {
        $actions_list = array( 'signin', 'signup', 'forget', 'reset_passwd' );

        $do = $this->__getPost('do');

        if( !in_array( $do, $actions_list ) ) $this->error( 'PARAMS_ERR' );

        /**
         * get email address or cellphone number which user typed in
         */
        $to = $this->__getPost('to');

        if( filter_var( $to, FILTER_VALIDATE_EMAIL ) ) {
            $type = 'email';
        } else if( preg_match( '#^\d{11}$#', $to ) ) {
            $type = 'phone';
        } else {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = array(
            'do' => $do,
            'to' => $to,
            'type' => $type
        );

        return $this;
    }
}
