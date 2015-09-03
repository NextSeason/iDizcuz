<?php

Class PostAction extends \Local\BaseAction {
    private $data;

    public function __execute() {
        $this->type = 'interface';

        $this->checkAccount()->paramsProcessing()->getTopicData();

        if( $this->pool['topic_data']['type'] == 1 ) {
            $this->checkPoint();
        }

        if( $this->params[ 'to' ] != 0 ) {
            $this->getToPostData();
        }

        $this->addPost()->sendMessage();

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
        if( $this->params[ 'to' ] == 0 ) return;

        $account_id = $this->pool['to_post_data']['account_id'];

        if( $account_id == $this->account['id'] ) return;

        \Message\Send::viewToMessage( $this->account['id'], $account_id, [ 'post' => [
            'id' => $this->data['id'],
            'title' => $this->params['title']
        ] ] );

        return $this;
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

        $transactionModel = new TransactionModel();

        $post_id = $transactionModel->addPost( $params );

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

        $content = $this->__getPost( 'content' );

        $contentTxt = strip_tags( $content );

        $len = strlen( $contentTxt );

        if( $len == 0 ) {
            $this->error( 'PARAMS_ERR' );
        }

        if( $len > 60000 ) {
            $this->error( 'CONTENT_TOOLONG' );
        }

        $content = \Local\EditorPurifier::purify( $content );

        $topic_id = $this->__getPost( 'topic_id' );

        if( is_null( $topic_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $point_id = $this->__getPost( 'point_id' );

        if( is_null( $point_id ) ) {
            $point_id = 0;
        }

        $title = $this->__getPost( 'title' );

        $post_id = intval( $this->__getPost( 'post_id' ) );

        $imagecode = $this->__getPost( 'imagecode' );

        if( is_null( $imagecode ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        if( strtolower( $this->session[ 'imagecode' ] ) != strtolower( $imagecode ) ) {
            $this->error( 'VCODE_ERR' );
        }

        $this->params = array(
            'content' => $content,
            'title' => $title,
            'to' => $post_id,
            'topic_id' => $topic_id,
            'point_id' => $point_id,
            'imagecode' => $imagecode
        );
        return $this;
    }
}
