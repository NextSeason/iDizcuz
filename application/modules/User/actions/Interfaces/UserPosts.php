<?php

Class UserPostsAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getPosts()->getTopics();

        return $this->data;
    }

    private function getTopics() {
        if( count( $this->data[ 'posts' ] ) == 0 ) {
            return $this;
        }

        $topicModel = new TopicModel();

        foreach( $this->data[ 'posts' ] as &$post ) {
            $post[ 'topic' ] = $topicModel->get( $post['topic_id'] );
        }

        return $this;
    }

    private function getPosts() {
        $postModel = new PostModel();

        $posts = $postModel->getPostsByAccount( $this->params[ 'account' ], '`id` DESC' );

        if( !$posts ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data[ 'posts'] = $posts;
        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $account = $request->getQuery( 'account' );

        if( is_null( $account ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $start = $request->getQuery( 'start' );

        $start = intval( $start );

        $rn = $request->getQuery( 'rn' );

        $rn = intval( $rn );

        if( $rn == 0 ) $rn = 20;

        if( $rn > 100 ) $rn = 100;

        $this->params = array(
            'account' => $account,
            'start' => $start,
            'rn' => $rn
        );

        return $this;
    }
}
