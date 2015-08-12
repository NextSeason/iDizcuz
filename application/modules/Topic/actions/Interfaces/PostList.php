<?php

Class PostListAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getPosts();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function getPosts() {
        $postDataModel = new PostDataModel();
        $posts_data = $postDataModel->gets( $this->params['ids'] );

        if( !count( $posts_data ) ) {
            $this->data['post'] = [];
        }

        $postModel = new PostModel();

        $accountModel = $this->accountModel ? $this->accountModel : new AccountModel();

        $posts = [];

        foreach( $posts_data as $post_data ) {
            $post = $postModel->get( $post_data['id'] );
            $post['data'] = $post_data;
            $post['mark'] = 0;

            if( $post['to'] != 0 ) {
                $to = $postModel->get( $post['to'] );
                if( $to ) {
                    $post['to'] = [
                        'id' => $to['id'],
                        'title' => $to['title']
                    ];
                }
            }

            $account = $accountModel->get( 
                $post['account_id'],
                ['id', 'uname', 'desc']
            );

            if( !$account ) {
                $this->error( 'SYSTEM_ERR' );
            }

            $post['account'] = $account;

            $posts[] = $post;
        }

        if( !is_null( $this->account ) ) {
            $markModel = new MarkModel();

            $account_id = $this->account['id'];

            foreach( $posts as &$post ) {
                $mark = $markModel->getMarkByPostAndAccount(
                    $post['id'],
                    $account_id
                );

                $post['mark'] = $mark ? $mark['id'] : 0;
                $post['own'] = $account_id == $post['account_id'] ? 1 : 0;
            }
        }

        $this->data['posts'] = $posts;

        return $this;
    }

    private function paramsProcessing() {
        $ids = $this->request->getQuery( 'ids' );

        if( is_null( $ids ) || !strlen( $ids ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params['ids'] = explode( ',', $ids );

        return $this;
    }
}
