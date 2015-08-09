<?php

Class PostsAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getPosts();

        return $this->data;
    }
    private function getPosts() {

        $postDataModel = new PostDataModel();

        $where = [
            [ 'status', 0 ]
        ];

        if( $this->params['cursor'] != 0 ) {
            $where[] = [ 'id', '<', $this->params['cursor'] ];
        }

        $posts_data = $postDataModel->select( [
            'where' => $where,
            'order' => [ [ 'id', 'DESC' ] ],
            'rn' => $this->params['rn']
        ] );

        if( $posts_data === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $postModel = new PostModel();

        $posts = $postModel->gets( \Local\Utils::pickByKey( $posts_data ) );

        // get topics
        $topicModel = new TopicModel();
        $topics = $topicModel->gets( \Local\Utils::pickByKey( $posts_data, 'topic_id' ), ['id', 'title'] );
        $topics = \Local\Utils::gather( $topics );

        // get accounts
        $accountModel = new AccountModel();
        $accounts = $accountModel->gets( \Local\Utils::pickByKey( $posts_data, 'account_id' ), ['id', 'uname'] );
        $accounts = \Local\Utils::gather( $accounts );

        $posts_data = \Local\Utils::gather( $posts_data );

        foreach( $posts as &$post ) {
            $post['data'] = $posts_data[ $post['id'] ];
            $post['account'] = $accounts[ $post['account_id'] ];
            $post['topic'] = $topics[ $post['topic_id'] ];
        }

        $this->data['posts'] = $posts;
        return $this;
    }

    private function paramsProcessing() {
        $cursor = intval( $this->request->getQuery('cursor') );
        if( $cursor < 0 ) $cursor = 0;

        $rn = intval( $this->request->getQuery( 'rn' ) );

        if( $rn < 1 ) $rn = 20;

        $this->params = [
            'cursor' => $cursor,
            'rn' => $rn
        ];

        return $this;
    }
}