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
        
        $res = $articleModel->update( $this->params['id'], $data );

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
        $request = $this->request;

        $title = $request->getPost( 'title' );

        if( is_null( $title ) || !strlen( $title ) ) {
            $this->error( 'PARAMS_ERR', 'event title is required' ); 
        }

        $topic_id = intval( $request->getPost( 'topic_id' ) );

        $time = $request->getPost( 'time' );

        if( is_null( $time ) || !strlen( $time ) ) {
            $time = date( 'Y-m-d', $_SERVER['REQUEST_TIME'] );
        } else {
            $time = date( 'Y-m-d', $time );
        }

        $this->params = [
            'id' => intval( $request->getPost('id') ),
            'topic_id' => $topic_id,
            'title' => $title,
            'img' => $request->getPost( 'img' ),
            'summary' => $request->getPost( 'summary' ),
            'time' => $time, 
            'origin' => $request->getPost( 'origin' ),
            'origin_url' => $request->getPost( 'origin_url' ),
            'origin_logo' => $request->getPost( 'origin_logo' ),
            'author' => $request->getPost( 'author' ),
            'content' => $request->getPost( 'content' )
        ];

        return $this;
    }
}
