<?php

Class PostAction extends \Local\BaseAction {
    private $data;

    private $transactionModel;

    public function __execute() {
        $this->type = 'interface';

        $this->checkAccount()->paramsProcessing()->getTopicData();

        if( $this->pool['topic_data']['type'] == 1 ) {
            $this->checkPoint();
        }

        if( $this->params[ 'to' ] != 0 ) {
            $this->getToPostData();
        }

        $this->transactionModel = new TransactionModel();
        
        $this->addPost();

        // if this post is for another post, send message to notice another account
        if( $this->params[ 'to' ] != 0 && $this->pool['to_post_data']['account_id'] != $this->account['id'] ) {
            $this->sendMessage();
        }

        $this->record( [
            'type' => 0,
            'relation_id' => $this->data['id']
        ] );

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
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

        $params = array(
            'content' => $params[ 'content' ],
            'topic_id' => $params[ 'topic_id' ],
            'point_id' => $params['point_id'],
            'title' => $params[ 'title' ],
            'to' => $params[ 'to' ],
            'account_id' => $this->account[ 'id' ],
            'ip' => ip2long( $_SERVER[ 'REMOTE_ADDR' ] )
        );

        $post_id = $this->transactionModel->addPost( $params );

        if( !$post_id ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data['id'] = $post_id;

        return $this;
    }

    private function checkPoint() {
        $point_id = intval( $this->params[ 'point_id' ] );
        if( is_null( $point_id ) || $point_id == 0 ) {
            $this->error( 'PARAMS_ERR' );
        }

        $topicModel = new TopicModel();

        $topic = $topicModel->get( $this->params[ 'topic_id' ], ['points'] );

        if( !in_array( $point_id, explode( ',', $topic['points'] ) ) ) {
            $this->error( 'POINT_NOTEXISTS' );
        }

        return $this;
    }

    private function getTopicData() {
        $topicDataModel = new TopicDataModel();

        $topic_id = $this->params[ 'topic_id' ];

        $topic_data = $topicDataModel->get( $topic_id, [ 'type', 'status' ] );

        if( !$topic_data || $topic_data['status'] == 0 ) {
            $this->error( 'TOPIC_NOTEXISTS' );
        }

        $this->pool['topic_data'] = $topic_data;

        return $this;
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

        if( $len == 0 ) {
            $this->error( 'PARAMS_ERR' );
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

        if( is_null( $point_id ) ) {
            $point_id = 0;
        }

        $title = $request->getPost( 'title' );

        $to = intval( $request->getPost( 'to' ) );

        $imagecode = $request->getPost( 'imagecode' );

        if( is_null( $imagecode ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        if( strtolower( $this->session[ 'imagecode' ] ) != strtolower( $imagecode ) ) {
            $this->error( 'VCODE_ERR' );
        }

        $this->params = array(
            'content' => $content,
            'title' => $title,
            'to' => $to,
            'topic_id' => $topic_id,
            'point_id' => $point_id,
            'imagecode' => $imagecode
        );

        return $this;
    }
}
