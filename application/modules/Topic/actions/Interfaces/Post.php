<?php

Class PostAction extends \Local\BaseAction {
    private $data;

    private $topicModel;
    private $pointModel;
    private $transactionModel;

    public function __execute() {
        $this->type = 'interface';

        $this->checkAccount()->paramsProcessing();

        $this->topicModel = new TopicModel();
        
        $topic = $this->getTopic();

        if( $topic[ 'type' ] == 1 ) {
            $this->pointModel = new PointModel();
            $this->checkPoint();
        }

        $this->transactionModel = new TransactionModel();
        
        $this->data[ 'id' ] = $this->addPost();

        return $this->data;
    }

    private function addPost() {
        $params = $this->params;

        $data = array(
            'content' => $params[ 'content' ],
            'topic_id' => $params[ 'topic_id' ],
            'title' => $params[ 'title' ],
            'to' => $params[ 'to' ],
            'account_id' => $this->account[ 'id' ],
            'ip' => ip2long( $_SERVER[ 'REMOTE_ADDR' ] )
        );

        if( isset( $params[ 'point_id ' ] ) ) {
            $data[ 'point_id' ] = $params[ 'point_id' ];
        }

        $post = $this->transactionModel->addPost( $data );

        if( !$post ) {
            $this->error( 'SYSTEM_ERR' );
        }

        return $post;
    }

    private function checkPoint() {
        $point_id = $this->params[ 'point_id' ];
        if( !$point_id ) {
            $this->error( 'PARAMS_ERR' );
        }

        $point = $this->pointModel->get( $point_id );

        if( !$point ) {
            $this->error( 'POINT_NOTEXISTS' );
        }

        return $this;
    }

    private function getTopic() {
        $topic_id = $this->params[ 'topic_id' ];
        $topic = $this->topicModel->get( $topic_id );

        if( !$topic ) {
            $this->error( 'TOPIC_NOTEXISTS' );
        }

        return $topic;
    }

    private function checkAccount() {
        $account = $this->session[ 'account' ];

        if( !$account ) {
            $this->error( 'NOT_LOGIN' );
        }

        if( intval( $account[ 'status' ] > 0 ) ) {
            $this->error( 'USER_BANNED' );
        }

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $content = $request->getPost( 'content' );

        $contentTxt = strip_tags( $content );

        $len = strlen( $contentTxt );

        if( $len < 10 ) {
            $this->error( 'CONTENT_TOOSHORT' );
        }

        if( $len > 60000 ) {
            $this->error( 'CONTENT_TOOLONG' );
        }

        $content = \Local\EditorPurifier::purify( $content );

        $topic_id = $request->getPost( 'topic_id' );

        if( is_null( $topic_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $point_id = $request->getPost( 'point_id' );

        $title = $request->getPost( 'title' );

        $to = intval( $request->getPost( 'to' ) );


        $this->params = array(
            'content' => $content,
            'title' => $title,
            'to' => $to,
            'topic_id' => $topic_id,
            'point_id' => $point_id
        );

        return $this;
    }
}
