<?php

Class PostAction extends \Local\BaseAction {

    private $data = array();

    private $topicModel;

    public function __execute() {

        $this->tpl = 'topic/topic';

        $this->paramsProcessing()->getPost()->getAccount()->getTopic()->reportReasons();

        return $this->data;
    }

    private function getAccount() {
        $accountModel = $this->accountModel ? $this->accountModel : new AccountModel(); 

        $account = $accountModel->get( $this->data['post']['account_id'], ['id','uname','desc'] );
        $this->data['post']['account'] = $account;

        return $this;
    }

    private function getPost() {
        $id = $this->params['id'];

        $postDataModel = new PostDataModel();

        $post_data = $postDataModel->get( $id );

        if( !$post_data ) {
            $this->data['post'] = false;
            return $this;
        }
        
        $postModel = new PostModel();
        $post = $postModel->get( $id );

        if( !$post ) {
            $this->redirect( '404' );
        }

        $post['mine'] = false;
        $post['marked'] = false;

        if( $this->account ) {
            if( $this->account['id'] == $post['account_id'] ) {
                $post['mine'] = true;
            }
            $markModel = new MarkModel();
            $mark = $markModel->getMarkByPostAndAccount( $id, $this->account['id'] );
            if( $mark ) {
                $post['marked'] = $mark['id'];
            }
        }
        if( $post['to'] != 0 ) {
            $to = $postModel->get( $post['to'], ['id', 'title'] );
            $post['to'] = $to;
        }

        $post['data'] = $post_data;

        $this->data['post'] = $post;

        return $this;
    }

    private function reportReasons() {
        $reportConf = \Local\Utils::loadConf( 'report', 'reasons' );
        $this->data[ 'reportReasons' ] = $reportConf;
        return $this;
    }

    private function getTopic() {
        $topicModel = new TopicModel();

        $topic = $topicModel->get( $this->data['post']['topic_id'] );

        $topicDataModel = new TopicDataModel();

        $topic['data'] = $topicDataModel->get( $topic[ 'id' ] );

        $this->data[ 'topic' ] = $topic;

        return $this;
    }

    private function paramsProcessing() {
        
        $id = $this->request->getParam( 'id' );

        if( is_null( $id ) ) {
            $this->redirect( '/' );
        }

        $this->params[ 'id' ] = $id;

        return $this;
    }
}
