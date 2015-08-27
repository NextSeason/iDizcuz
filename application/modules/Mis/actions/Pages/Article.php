<?php

Class ArticleAction extends \Local\MisAction {
    private $data = [
        'article' => [
            'id' => 0,
            'title' => '',
            'img' => '',
            'topic_id' => 0,
            'summary' => '',
            'img' => '',
            'origin' => '',
            'origin_url' => '',
            'origin_logo' => '',
            'time' => '',
            'content' => '',
            'author' => ''
        ]
    ];

    public function __execute() {

        $this->tpl = 'mis/article';

        $this->paramsProcessing();

        if( $this->params['id'] != 0 ) {
            $this->getArticle();
        }

        return $this->data;
    }

    private function getArticle() {
        $id = $this->params['id'];

        $articleModel = new ArticleModel();

        $article = $articleModel->get( $id );

        if( !$article ) {
            return $this;
        }

        $this->data['article'] = $article;
        return $this;
    }

    private function paramsProcessing() {
        $id = intval( $this->__getQuery( 'id' ) );

        $this->params = [
            'id' => $id
        ];

        return $this;
    }
}
