<?php

Class PostAction extends \Local\MisAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'mis/post';

        $this->paramsProcessing()->getPost()->getAccount();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function getAccount() {
        if( isset( $this->data['post'] ) ) {
            $this->data['post']['account'] = \Accounts\Api::get( $this->data['post']['account_id'] );
        }

        return $this;
    }

    private function getPost() {
        if( is_null( $this->params['post_id'] ) ) {
            $this->data['post'] = null;
            return $this;
        }

        $post = \Posts\Api::get( $this->params['post_id'] );

        if( !$post ) {
            return $this;
        }

        $post['data'] = \Posts\Api::getData( $this->params['post_id'] );

        $this->data['post'] = $post;

        return $this;
    }

    private function paramsProcessing() {
        $post_id = $this->__getQuery( 'id' );

        $this->params = [
            'post_id' => $post_id
        ];
        return $this;
    }
}
