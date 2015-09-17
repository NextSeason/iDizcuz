<?php

Class UserAction extends \Local\MisAction {
    private $data = [];
    protected $tpl = 'mis/user';

    public function __execute() {
        $this->paramsProcessing()->getUser();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function getUser() {
        $user = \Accounts\Api::get( $this->params['user_id'] );
        if( !$user ) {
            return $this;
        }

        $user['data'] = \Accounts\Api::getData( $this->params['user_id'] );

        $this->data['user'] = $user;

        return $this;
    }

    private function paramsProcessing() {
        $user_id = $this->__getQuery( 'id' );

        $this->params = [
            'user_id' => $user_id
        ];
        return $this;
    }
}
