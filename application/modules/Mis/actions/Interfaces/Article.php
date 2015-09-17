<?php

Class ArticleAction extends \Local\MisAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing();

        if( $this->params['topic_id'] != 0 ) {
            $this->checkTopic();
        }
        
        if( $this->params['id'] != 0 ) {
            $this->updateArticle();
        } else {
            $this->addArticle();
        }

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function checkTopic() {
        return $this;
    }

    private function updateArticle() {
        $params = $this->params;

        $data = [
            'topic_id' => $params['topic_id'],
            'title' => $params[ 'title' ],
            'content' => $params[ 'content' ],
            'img' => $params[ 'img' ],
            'summary' => $params['summary'],
            'time' => $params['time'],
            'origin' => $params['origin'],
            'origin_url' => $params['origin_url'],
            'origin_logo' => $params['origin_logo'],
            'author' => $params['author']
        ];

        $articleModel = new ArticleModel();
        
        $res = $articleModel->update( [
            'set' => $data,
            'where' => [
                [ 'id', $this->params['id'] ]
            ]
        ] );

        if( !$res ) {
            $this->error('SYSTEM_ERR' );
        }

        return $this;

    }

    private function addArticle() {
        $params = $this->params;

        $data = [
            'topic_id' => $params['topic_id'],
            'title' => $params[ 'title' ],
            'content' => $params[ 'content' ],
            'img' => $params[ 'img' ],
            'summary' => $params['summary'],
            'time' => $params['time'],
            'origin' => $params['origin'],
            'origin_url' => $params['origin_url'],
            'origin_logo' => $params['origin_logo'],
            'author' => $params['author']
        ];

        $articleModel = new ArticleModel();

        $article_id = $articleModel->insert( $data );

        if( !$article_id ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data['article_id'] = $article_id;

        return $this;
    }

    private function paramsProcessing() {
        $title = $this->__getPost( 'title' );

        if( is_null( $title ) || !strlen( $title ) ) {
            $this->error( 'PARAMS_ERR', 'event title is required' ); 
        }

        $topic_id = intval( $this->__getPost( 'topic_id' ) );

        $time = $this->__getPost( 'time' );

        if( is_null( $time ) || !strlen( $time ) ) {
            $time = date( 'Y-m-d', $_SERVER['REQUEST_TIME'] );
        } else {
            $time = date( 'Y-m-d', $time );
        }

        $this->params = [
            'id' => intval( $this->__getPost('id') ),
            'topic_id' => $topic_id,
            'title' => $title,
            'img' => $this->__getPost( 'img' ),
            'summary' => $this->__getPost( 'summary' ),
            'time' => $time, 
            'origin' => $this->__getPost( 'origin' ),
            'origin_url' => $this->__getPost( 'origin_url' ),
            'origin_logo' => $this->__getPost( 'origin_logo' ),
            'author' => $this->__getPost( 'author' ),
            'content' => $this->__getPost( 'content' )
        ];

        return $this;
    }
}
