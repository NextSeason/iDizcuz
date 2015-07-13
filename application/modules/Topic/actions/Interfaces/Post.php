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

        if( $this->params[ 'to' ] != 0 ) {
            $this->getToPostData();
        }

        $this->transactionModel = new TransactionModel();
        
        $this->data[ 'id' ] = $this->addPost();

        if( $this->params[ 'to' ] != 0 && $this->pool['to_post_data']['account_id'] != $this->account['id'] ) {
            $this->sendMessage();
        }

        return $this->data;
    }

    private function sendMessage() {
        $view = $this->getView();

        $conf = \Local\Utils::loadConf( 'message', 'post' );

        $data = [
            'from' => 0,
            'to' => $this->pool[ 'to_post_data' ][ 'account_id' ],
            'type' => $conf->type,
            'title' => $view->render( $conf->template, array(
                '_part' => 'title',
                'account' => $this->account
            ) ),
            'content' => $view->render( $conf->template, array(
                '_part' => 'content',
                'account' => $this->account,
                'post' => array(
                    'id' => $this->data['id'],
                    'ctime' => date( 'Y-m-d H:i:s', $_SERVER[ 'REQUEST_TIME' ] ),
                    'title' => $this->params[ 'title' ]
                )
            ) )
        ];
        $this->transactionModel->sendMessage( $data );
    }

    private function getToPostData() {
        $postDataModel = new PostDataModel();
        $post_data = $postDataModel->get( $this->params[ 'to' ] );

        if( !$post_data ) {
            $this->error( 'POST_NOTEXISTS' );
        }

        $this->pool['to_post_data'] = $post_data;
        return $this;
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
            $this->error( 'NOTLOGIN_ERR' );
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
