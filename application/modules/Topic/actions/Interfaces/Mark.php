<?php

Class MarkAction extends \Local\BaseAction {
    private $data = array();

    public function __execute() {
        $this->type = 'interface';

        if( is_null( $this->account ) ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->checkPost()->action();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function action() {
        $params = $this->params;

        $act = $params[ 'act' ];

        $transactionModel = new TransactionModel();

        if( $act == 0 ) {
            $res = $transactionModel->removeMark( [
                'post_id' => $params['post_id'],
                'account_id' => $this->account['id']
            ] );

            if( !$res ) $this->error( 'SYSTEM_ERR' );

            return $this;
        }
        if( $act == 1 ) {
            $markModel = new MarkModel();

            $mark = $markModel->select( [
                'where' => [
                    [ 'post_id', $params[ 'post_id'] ],
                    [ 'accout_id', $this->account['id'] ]
                ]
            ] );

            if( !$mark ) {
                $mark = $transactionModel->addMark( array(
                    'account_id' => $this->account[ 'id' ],
                    'post_id' => $params[ 'post_id' ]
                ) );

                if( !$mark ) $this->error( 'SYSTEM_ERR' );
                $this->data['mark'] = $mark;
            }
        }

        return $this;
    }

    private function checkPost() {
        $postDataModel = new PostDataModel();

        $post = $postDataModel->get( $this->params[ 'post_id' ] );

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

        $this->params = array(
            'post_id' => $post_id,
            'act' => $act
        );

        return $this;
    }
}
