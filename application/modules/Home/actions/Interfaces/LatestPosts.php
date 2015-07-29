<?php

Class LatestPostsAction extends \Local\BaseAction {

    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getActivities()->getPostsData()->getPosts();
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
        $ids = $this->pool['ids'];

        $postDataModel = new PostDataModel();

        $posts_data = $postDataModel->gets( $ids );

        $this->pool['posts_data'] = $posts_data;

        return $this;
    }

    private function getActivities() {
        $activityModel = new ActivityModel();

        $activities = $activityModel->select( [
            'where' => [
                [ 'type', 0 ]
            ],
            'rn' => $this->params['rn']
        ] );

        if( !$activities ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $ids = [];

        foreach( $activities as $activity ) {
            $ids[] = $activity['relation_id'];
        }

        $this->pool['ids'] = $ids;

        return $this;
    }

    private function paramsProcessing() {
        $rn = $this->request->getQuery( 'rn' );

        $this->params = [
            'rn' => $rn
        ];

        return $this;
    }
}
