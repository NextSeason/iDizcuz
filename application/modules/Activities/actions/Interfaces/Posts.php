<?php

Class PostsAction extends \Local\BaseAction {
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
        $topic_ids = [];
        
        foreach( $posts_data as $post_data ) {
            $formated_posts_data[ $post_data['id'] ] = $post_data;
            $account_ids[] = $post_data['account_id'];
            $topic_ids[] = $post_data['topic_id'];
        }


        // get post
        $postModel = new PostModel();

        $posts = $postModel->gets( $ids );


        // get posts accounts
        $accountModel = new AccountModel();

        $account_ids = array_unique( $account_ids );

        $accounts = $accountModel->gets( $account_ids, [ 'id', 'uname' ] );

        $formated_accounts_data = [];

        foreach( $accounts as $account ) {
            $formated_accounts_data[ $account['id'] ] = $account;
        }

        // get posts topic
        $topicModel = new TopicModel(); 

        $topic_ids = array_unique( $topic_ids );

        $topics = $topicModel->gets( $topic_ids, [ 'id', 'title' ] );

        $formated_topics_data = [];

        foreach( $topics as $topic ) {
            $formated_topics_data[ $topic['id'] ] = $topic;
        }

        foreach( $posts as &$post ) {
            $post['data'] = $formated_posts_data[ $post['id'] ];
            $post['account'] = $formated_accounts_data[ $post['account_id'] ];
            $post['topic'] = $formated_topics_data[ $post['topic_id'] ];
        }

        $this->data['posts'] = $posts;

        return $this;
    }

    private function getActivities() {
        $params = $this->params;

        $activityModel = new ActivityModel();

        $where = [
            [ 'type', '0' ]
        ];

        if( $params['cursor'] != 0 ) {
            $where[] = [ 'id', '<', $params['cursor'] ];
        }

        $activities = $activityModel->select( [
            'where' => $where,
            'order' => [ [ 'id', 'DESC' ] ],
            'rn' => $params['rn']
        ] );

        if( $activities === false ) {
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
