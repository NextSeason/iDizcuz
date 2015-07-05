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


        $this->params = array(
            'account' => $account
        );

        return $this;
    }
}
