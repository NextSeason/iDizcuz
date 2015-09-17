<?php

Class UnbanUserAction extends \Local\MisAction {
    private $data = [];
    protected $type = 'interface';

    public function __execute() {
        $this->paramsProcessing()->unban();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function unban() {
        $res = \Accounts\Api::unban(
            $this->params['user_id']
        );

        if( !$res ) $this->error( 'SYSTEM_ERR' );

        return $this;
    }

    private function paramsProcessing() {
        $user_id = $this->__getPost( 'user_id' );

        if( is_null( $user_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = [
            'user_id' => $user_id
        ];
        return $this;
    }
}
