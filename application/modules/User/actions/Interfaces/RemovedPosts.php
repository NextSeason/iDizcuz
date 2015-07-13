<?php

Class RemovedPostsAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->getPosts();

        return $this->data;
    }

    private function getPosts() {
        $params = $this->params;

        $postDataModel = new PostDataModel();

        $posts_data = $postDataModel->getRemovedPostsByAccount(
            $this->account['id'],
            $params[ 'status' ],
            $params[ 'start' ],
            $params[ 'rn' ]
        );

        if( $posts_data === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        if( count( $posts_data ) == 0 ) {
            $this->data['posts'] = [];
            return $this;
        }

        $postModel = new PostModel();

        $posts = [];

        foreach( $posts_data as $post_data ) {
            $post = $postModel->get( $post_data['id'] );
            $post['data'] = $post_data;
            $posts[] = $post;
        }

        $this->data[ 'posts' ] = $posts;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $start = intval( $request->getQuery( 'start' ) );

        if( $start < 0 ) $start = 0;

        $rn = intval( $request->getQuery( 'rn' ) );

        if( $rn <= 0 ) $rn = 20;
        if( $rn > 100 ) $rn = 100;

        $status = $request->getQuery( 'status' );

        $this->params = [
            'start' => $start,
            'rn' => $rn,
            'status' => $status
        ];

        return $this;
    }
}
