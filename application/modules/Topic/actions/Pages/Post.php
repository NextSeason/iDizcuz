<?php

Class PostAction extends \Local\BaseAction {

    private $data = [
        'nav' => 'topic'
    ];

    private $topicModel;

    public function __execute() {

        $this->tpl = 'topic/topic';

        $this->paramsProcessing();
        
        if( $this->getPost() === false ) {
            $this->tpl = 'topic/none';
            return $this->data;
        }
        
        $this->getAccount()->getTopic()->getPoints()->reportReasons();

        return $this->data;
    }

    public function __mobile() {
        $this->tpl = 'topicMobile/post';

        $this->paramsProcessing()->getPost()->getTopic()->getAccount();
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

        if( !$post_data ) return false; 
        
        $postModel = new PostModel();
        $post = $postModel->get( $id );

        if( !$post ) return false;

        $post['own'] = false;
        $post['mark'] = false;

        if( $this->account ) {
            if( $this->account['id'] == $post['account_id'] ) {
                $post['own'] = true;
            }
            $markModel = new MarkModel();
            $mark = $markModel->getMarkByPostAndAccount( $id, $this->account['id'] );
            if( $mark ) {
                $post['mark'] = true;
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

    private function getPoints() {
        $topic = $this->data['topic'];
        if( $topic['data']['type'] == 0 ) {
            $this->data['points'] = [];
            return $this;
        }
        $points = $topic['points'];

        $pointModel = new PointModel();

        $points = $pointModel->gets( explode( ',', $points ) );

        if( !$points ) {
            //error
        }

        $pointDataModel = new PointDataModel();

        foreach( $points as &$point ) {
            $point_data = $pointDataModel->get( $point['id'] );
            $point_data['index'] = \Local\Utils::pointIndex( $point_data );
            $point['data'] = $point_data;
        }

        $this->data['points'] = $points;
        return $this;

    }

    private function getTopic() {
        $topicModel = new TopicModel();

        $topic = $topicModel->get( $this->data['post']['topic_id'] );

        $topicDataModel = new TopicDataModel();

        $topic['data'] = $topicDataModel->get( $topic[ 'id' ] );

        $categories = \Local\Utils::loadConf( 'categories', 'list' );

        $topic['data']['cate'] = $categories[$topic['data']['cid']];

        $this->data[ 'topic' ] = $topic;

        return $this;
    }

    private function paramsProcessing() {
        
        $id = $this->__getParam( 'id' );

        if( is_null( $id ) ) {
            $this->redirect( '/' );
            exit;
        }

        $this->params[ 'id' ] = $id;

        return $this;
    }
}
