<?php

Class ArticlesAction extends \Local\MisAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'mis/articles';

        $this->paramsProcessing()->getArticles();

        return $this->data;
    }

    private function getArticles() {
        $params = $this->params;
        $articleModel = new ArticleModel();
        $articles = $articleModel->getArticles( [
            'start' => $params['start'],
            'rn' => $params['rn']
        ] );

        if( $articles === false ) {
            $this->data['articles'] = [];
            return $this;
        }

        $this->data['articles'] = $articles;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $start = intval( $request->getQuery( 'start' ) );
        $rn = intval( $request->getQuery( 'rn' ) );

        if( $rn < 1 ) $rn = 20;

        $this->params = [
            'start' => $start,
            'rn' => $rn
        ];
        return $this;
    }
}