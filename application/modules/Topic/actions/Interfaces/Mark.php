<?php

Class MarkAction extends \Local\BaseAction {
    private $data = array();

    private $postDataModel;

    private $markModel;

    public function __execute() {
        $this->type = 'interface';

        if( is_null( $this->account ) ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing();
        
        $this->postDataModel = new PostDataModel();
        
        $this->checkPost();

        $this->markModel = new MarkModel();

        $this->action();

        return $this->data;
    }

    private function action() {
        $params = $this->params;

        $act = $params[ 'act' ];

        if( $act == 0 ) {
            $res = $this->markModel->remove( $params[ 'mark_id' ] );

            if( !$res ) $this->error( 'SYSTEM_ERR' );

            return $this;
        }
        if( $act == 1 ) {
            $mark = $this->markModel->getMarkByPostAndAccount( $params[ 'post_id' ], $this->account[ 'id' ] );

            if( $mark ) {
                $this->data[ 'mark' ] = $mark[ 'id' ];
            } else {

                $mark = $this->markModel->insert( array(
                    'account_id' => $this->account[ 'id' ],
                    'post_id' => $params[ 'post_id' ]
                ) );

                if( !$mark ) $this->error( 'SYSTEM_ERR' );

                $this->data[ 'mark' ] = $mark;
            }
        }

        return $this;
    }

    private function checkPost() {
        $post = $this->postDataModel->get( $this->params[ 'post_id' ] );
        if( !$post ) {
            $this->error( 'POST_NOTEXISTS' );
        }

        return $this;
    }

    private function paramsProcessing() {

        $request = $this->request;

        $post_id = $request->getPost( 'post_id' );

        if( is_null( $post_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $act = $request->getPost( 'act' );

        if( is_null( $act ) || !in_array( $act, [ 0, 1 ] ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $mark_id = $request->getPost( 'mark_id' );

        if( $act == 0 && is_null( $mark_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = array(
            'post_id' => $post_id,
            'mark_id' => $mark_id,
            'act' => $act
        );

        return $this;
    }
}
