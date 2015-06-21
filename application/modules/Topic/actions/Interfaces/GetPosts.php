<?php

Class GetPostsAction extends \Local\BaseAction {

    private $postDataModel;
    private $postModel;

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing();

        $this->postDataModel = new PostDataModel();
        $this->postModel = new PostModel();

        $posts = $this->getPosts();

        return [ 'posts' => $posts ];
    }

    private function getPosts() {
        $params = $this->params;

        if( !is_null( $params[ 'point_id' ] ) ) {
            $postsData = $this->postDataModel->getPostsByPoint( $params[ 'point_id' ], $params[ 'order' ], $params[ 'start' ], $params[ 'len' ] );
        } else {
            $postsData = $this->postDataModel->getPostsByTopic( $params[ 'topic_id' ], $params[ 'order' ], $params[ 'start' ], $params[ 'len' ] );
        }

        if( $postsData === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $posts = [];

        foreach( $postsData as $postData ) {
            $post = $this->postModel->get( $postData[ 'id' ] );
            $post[ 'data' ] = $postData;
            $posts[] = $post;
        }

        $accountModel = new AccountModel();

        foreach( $posts as &$post ) {
            $author = $accountModel->get( $post[ 'author_id' ], [ 'id', 'uname', 'desc' ] );
            if( !$author ) {
                $this->error( 'SYSTEM_ERR' );
            }
            $post[ 'author' ] = $author;
        }

        return $posts;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $topic_id = $request->getQuery( 'topic' );

        if( is_null( $topic_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $point_id = $request->getQuery( 'point' );

        $order = $request->getQuery( 'order' );

        $orderList = [ '`id` DESC', '`agree` DESC', '`disagree` DESC' ];

        $order = is_null( $order ) ? '`id` DESC' : $orderList[ $order ];

        $start = $request->getQuery( 's' );

        if( is_null( $start ) ) $start = 0;

        $len = $request->getQuery( 'l' );

        if( is_null( $len ) ) $len = 20;

        if( $len > 100 ) $len = 100;

        $this->params = [
            'topic_id' => $topic_id,
            'point_id' => $point_id,
            'order' => $order,
            'start' => $start,
            'len' => $len
        ];

        return $this;
    }
}
