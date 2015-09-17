<?php

Class BanUserAction extends \Local\MisAction {
    private $data = [];
    protected $type = 'interface';

    public function __execute() {
        $this->paramsProcessing()->ban();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function ban() {
        $res = \Accounts\Api::ban(
            $this->params['user_id'],
            date( 'Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ),
            date( 'Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] + $this->params['time'] * 3600 )
        );

        if( !$res ) $this->error( 'SYSTEM_ERR' );

        return $this;
    }

    private function paramsProcessing() {
        $user_id = $this->__getPost( 'user_id' );

        if( is_null( $user_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $time = intval( $this->__getPost( 'time' ) );

        if( $time == 0 ) {
            $this->error( 'PARAMS_ERR' );
        }

        $message = $this->__getPost( 'message' );

        $this->params = [
            'user_id' => $user_id,
            'time' => $time,
            'message' => $message
        ];

        return $this;
    }
}
