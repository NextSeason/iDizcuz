<?php

Class ArticleAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'article/article';
        $this->paramsProcessing()->getArticle()->getTopic()->getRelated();
        return $this->data;
    }

    public function __mobile() {
        $this->tpl = 'articleMobile/article';
        $this->paramsProcessing()->getArticle()->getTopic()->getRelated();
        return $this->data;
    }

    private function getRelated() {
        $articleModel = new ArticleModel();

        $related_articles = $articleModel->select( [
            'columns' => [ 'id', 'title', 'time', 'img', 'origin', 'origin_url', 'author' ],
            'where' => [
                [ 'topic_id', $this->data['topic']['id'] ]
            ],
            'order' => [ 'RAND()' ],
            'rn' => 6
        ] );

        $this->data[ 'related_articles' ] = $related_articles;
        return $this;

    }

    private function getTopic() {
        if( is_null( $this->data['article' ] ) || $this->data['article']['topic_id'] == 0 ) {
            $this->data['topic'] = null;
        }

        $topicModel = new TopicModel();

        $topic = $topicModel->get( $this->data['article']['topic_id'], [ 'id', 'title' ] );

        $this->data['topic'] = $topic;

        return $this;
    }

    private function getArticle() {
        $articleModel = new ArticleModel();

        $article = $articleModel->get( $this->params['id'] );

        if( !$article ) {
            $this->data['article'] = null;
        }

        $this->data['article'] = $article;
        return $this;
    }

    private function paramsProcessing() {
        $id = $this->__getParam( 'id' );

        $this->params = [
            'id' => $id
        ];

        return $this;
    }
}
