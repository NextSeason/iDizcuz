<?php

Class GetCommentsAction extends \Local\BaseAction {

    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getComments();

        return $this->data;
    }

    private function getComments() {
        $params = $this->params;

        $commentModel = new CommentModel();

        $comments = $commentModel->getCommentsByPost(
            $params[ 'post_id' ],
            $params[ 'start' ],
            $params[ 'rn' ]
        );

        if( $comments === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data[ 'comments' ] = $comments;
        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $post_id = $request->getQuery( 'post_id' );

        if( is_null( $post_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $start = intval( $request->getQuery( 'start' ) );

        $rn = intval( $request->getQuery( 'rn' ) );

        if( $rn == 0 ) $rn = 20;
        if( $rn > 100 ) $rn = 100;

        $this->params = [
            'post_id' => $post_id,
            'start' => $start,
            'rn' => $rn
        ];

        return $this;
    }
}
