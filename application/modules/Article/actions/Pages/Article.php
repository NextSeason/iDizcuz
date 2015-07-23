<?php

Class ArticleAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'article/article';
        $this->paramsProcessing()->getArticle()->getTopic()->getAdjacent();
        return $this->data;
    }

    private function getAdjacent() {
        $topic = $this->data['topic'];

        if( !$topic ) {
            $this->data['pre'] = null;
            $this->data['next'] = null;
        }

        $articleModel = new ArticleModel();

        $this->data['pre'] = $articleModel->getPrevious( $this->params['id'], $topic['id'] );
        $this->data['next'] = $articleModel->getNext( $this->params['id'], $topic['id'] );

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
        $id = $this->request->getParam( 'id' );

        $this->params = [
            'id' => $id
        ];

        return $this;
    }
}
