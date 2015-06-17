<?php

Class PublicAction extends \Local\BaseAction {
    private $data;

    public function __execute() {
        $this->type = 'interface';

        $this->checkAccount()->paramsProcessing()->addPost();

        return $this->data;
    }

    private function checkAccount() {
        $account = $this->session[ 'account' ];

        if( !$account ) {
            $this->error( 'NOT_LOGIN' );
        }

        if( intval( $account[ 'status' ] > 0 ) ) {
            $this->error( 'USER_BANNED' );
        }

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $content = $request->getPost( 'content' );

        $contentTxt = strip_tags( $content );

        $len = strlen( $contentTxt );

        if( $len < 20 || $len > 60000 ) {
            $this->error( 'PARAMS_ERROR' );
        }

        $content = \Local\EditorPurifier::purify( $content );

        $this->params = array(
            content : $content;
        );

        return $this;

    }

    private function addPost() {
    }
}
