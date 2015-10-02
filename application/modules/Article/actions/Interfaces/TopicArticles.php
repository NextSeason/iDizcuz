<?php 

Class TopicArticlesAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->getArticles();
        return $this->data;
    }


    public function __mobile() {
        return $this->__execute();
    }

    private function getArticles() {
        $articleMobile = new ArticleModel();

        $articles = $articleMobile->select( [
            'columns' => [ 'id', 'img', 'title', 'summary', 'time', 'origin', 'origin_url', 'author' ],
            'where' => [
                [ 'topic_id', $this->params['topic_id'] ]
            ],
            'order' => [ [ 'time', 'DESC' ] ],
            'start' => $this->params['start'],
            'rn' => $this->params['rn'] 
        ] );

        if( $articles === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data['articles'] = $articles;
        return $this;
    }

    private function paramsProcessing() {
        $topic_id = $this->__getQuery( 'topic_id' );

        if( is_null( $topic_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $pn = intval( $this->__getQuery( 'pn' ) );

        if( $pn <= 0 ) $pn = 1;

        $rn = intval( $this->__getQuery( 'rn' ) );

        if( $rn == 0 ) $rn = 40;
        if( $rn > 100 ) $rn = 100;

        $this->params = [
            'topic_id' => $topic_id,
            'pn' => $pn,
            'rn' => $rn,
            'start' => ( $pn - 1 ) * $rn
        ];

        return $this;
    }
}
