<?php

Class LatestPostsAction extends \Local\BaseAction {

    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getActivities()->getPosts();
        return $this->data;
    }

    private function getPosts() {
        $ids = $this->pool['ids'];

        $postDataModel = new PostDataModel();

        $posts_data = $postDataModel->gets( $ids );

        if( !$posts_data ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $formated_posts_data = [];
        $account_ids = [];
        
        foreach( $posts_data as $post_data ) {
            $formated_posts_data[ $post_data['id'] ] = $post_data;
            $account_ids[] = $post_data['account_id'];
        }

        $account_ids = array_unique( $account_ids );

        $postModel = new PostModel();

        $posts = $postModel->gets( $ids, [ 'id', 'title', 'account_id' ] );

        $accountModel = new AccountModel();

        $accounts = $accountModel->gets( $account_ids, [ 'id', 'uname' ] );

        $formated_accounts_data = [];

        foreach( $accounts as $account ) {
            $formated_accounts_data[ $account['id'] ] = $account;
        }

        foreach( $posts as &$post ) {
            $post['data'] = $formated_posts_data[ $post['id'] ];
            $post['account'] = $formated_accounts_data[ $post['account_id'] ];
        }

        $this->data['posts'] = $posts;

        return $this;
    }

    private function getActivities() {
        $activityModel = new ActivityModel();

        $activities = $activityModel->select( [
            'where' => [
                [ 'type', 0 ]
            ],
            'order' => [ [ 'id', 'DESC' ] ],
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
        $rn = $this->__getQuery( 'rn' );

        $this->params = [
            'rn' => $rn
        ];

        return $this;
    }
}
