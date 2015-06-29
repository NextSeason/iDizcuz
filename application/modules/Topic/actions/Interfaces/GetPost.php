<?php 

Class GetPostAction extends \Local\BaseAction {
    private $data = [];
    private $postModel;

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing();

        $this->postModel = new PostModel();

        $this->getPost();

        return $this->data;
    }

    private function getPost() {
        $post = $this->postModel->get( $this->params[ 'id' ] );

        if( !$post ) {
            $this->error( 'POST_NOTEXISTS' );
        }

        $postDataModel = new PostDataModel();

        $post[ 'data' ] = $postDataModel->get( $this->params[ 'id' ] );

        if( !is_null( $this->account ) ) {
            $markModel = new MarkModel();

            $account_id = $this->account[ 'id' ];

            $mark = $markModel->getMarkByPostAndAccount( $post['id'], $account_id );

            $post[ 'mark' ] = $mark ? $mark[ 'id' ] : 0;
        }

        $accountModel = new AccountModel();

        $account = $accountModel->get( $post[ 'account_id' ], [ 'id', 'uname', 'desc' ] );

        $post[ 'account' ] = $account;

        $this->data[ 'post' ] = $post;
        return $this;
    }

    private function paramsProcessing() {
        $id = $this->request->getQuery( 'id' );

        if( is_null( $id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = [
            'id' => $id
        ];

        return $this;
    }
}
