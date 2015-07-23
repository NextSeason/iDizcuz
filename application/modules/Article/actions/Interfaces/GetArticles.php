<?php 

Class GetArticlesAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getArticles();
        return $this->data;
    }

    private function getArticles() {
        $params = $this->params;

        $articleModel = new ArticleModel();
        $articles = $articleModel->getArticlesByTopic( [
            'topic_id' => $params[ 'topic_id' ],
            'cursor' => $params['cursor'],
            'rn' => $params['rn']
        ] );

        if( $articles === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data['articles'] = $articles;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $topic_id = $request->getQuery( 'topic_id' );

        if( is_null( $topic_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $cursor = intval( $request->getQuery( 'cursor' ) );

        $rn = intval( $request->getQuery( 'rn' ) );

        if( $rn <= 0 ) $rn = 20;

        $this->params = [
            'topic_id' => $topic_id,
            'cursor' => $cursor,
            'rn' => $rn
        ];

        return $this;
    }
}
