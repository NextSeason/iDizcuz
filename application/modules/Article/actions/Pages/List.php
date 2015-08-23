<?php

Class ListAction extends \Local\BaseAction {
    private $data = [];
    private $rn = 40;

    public function __execute() {
        $this->tpl = 'article/list';
        $this->paramsProcessing()->getTopic()->getArticles();
        return $this->data;
    }

    public function __mobile() {
        $this->tpl = 'articleMobile/list';
        $this->paramsProcessing()->getTopic()->getArticles();
        return $this->data;
    }

    private function getArticles() {
        $articleModel = new ArticleModel();

        $articles = $articleModel->select( [
            'columns' => [ 'id', 'img', 'title', 'summary', 'time', 'origin', 'origin_url', 'author' ],
            'where' => [
                [ 'topic_id', $this->params['id'] ]
            ],
            'order' => [ [ 'time', 'DESC' ] ],
            'start' => $this->params['start'],
            'rn' => $this->rn 
        ] );

        if( $articles === false ) {
            //echo error;
        }

        $this->data['articles'] = $articles;

        return $this;
    }

    private function getTopic() {
        $id = $this->params['id'];
        $topicDataModel = new TopicDataModel();

        $topic_data = $topicDataModel->get( $id, ['id', 'status'] );

        if( !$topic_data || $topic_data['status'] != 1 ) {
            // 404
        }

        $topicModel = new TopicModel();

        $topic = $topicModel->get( $id, ['id','title'] );

        $topic['data'] = $topic_data;

        $this->data['topic'] = $topic;
        
        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $id = $request->getParam( 'id' );

        $pn = intval( $request->getParam( 'pn' ) );

        if( $pn < 1 ) $pn = 1;

        $start = ( $pn - 1 ) * $this->rn;

        $this->params = [
            'id' => $id,
            'start' => $start
        ];

        return $this;
    }
}
