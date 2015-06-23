<?php

Class VoteAction extends \Local\BaseAction {
    private $data = array();

    public function __execute() {
        $this->type = 'interface';

        if( is_null( $this->account ) ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing();

        return $this->data;
    }

    private function paramsProcessing() {
        $request = $this->request;
    }
}
