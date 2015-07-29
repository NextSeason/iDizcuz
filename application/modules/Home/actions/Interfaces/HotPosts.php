<?php

Class HotPostsAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';
        $this->paramsProcessing()->getPostsData()->getPosts();

        return $this->data;
    }

    private function getPosts() {
        $postModel = new PostModel();
        $accountModel = new AccountModel();

        $posts = [];

        foreach( $this->pool['posts_data'] as $post_data ) {
            $post = $postModel->get( $post_data['id'], [ 'id', 'title', 'account_id' ] );
            $post['data'] = $post_data;
            $post['account'] = $accountModel->get( $post['account_id'], [ 'id', 'uname' ] );
            $posts[] = $post;
        }

        $this->data['posts'] = $posts;
        return $this;
    }

    private function getPostsData() {
        $postDataModel = new PostDataModel();

        $posts_data = $postDataModel->select( [
            'columns' => [ 'id', 'agree' ],
            'where' => [
                [ 'topic_id', $this->params['topic_id'] ],
                [ 'status', 0 ]
            ],
            'order' => [
                [ 'id', 'DESC' ]
            ],
            'rn' => $this->params['rn']
        ] );

        $this->pool['posts_data'] = $posts_data;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;
        $topic_id = $request->getQuery( 'topic_id' );

        if( is_null( $topic_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $rn = intval( $request->getQuery( 'rn' ) );

        $this->params = [
            'topic_id' => $topic_id,
            'rn' => $rn
        ];

        return $this;
    }
}
