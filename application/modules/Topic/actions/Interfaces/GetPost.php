<?php 

Class GetPostAction extends \Local\BaseAction {
    private $data = [];
    private $postModel;

    public function __execute() {
        $this->type = 'interface';
        $this->paramsProcessing()->getPost()->getAccount()->getTopic()->getPoint()->getMark();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute(); 
    }

    private function getMark() {
        $markModel = new MarkModel();
    }

    private function getPoint() {
        if( $this->data['post']['point_id'] == 0 ) {
            return $this;
        }
        $pointModel = new PointModel();

        $point = $pointModel->get( $this->data['post']['point_id'], 'title' );

        $this->data['post']['point'] = $point;

        return $this;
    }

    private function getTopic() {
        $topicModel = new TopicModel();
        $topic = $topicModel->get( $this->data['post']['topic_id'], [ 'id', 'title' ] );

        $this->data['post']['topic'] = $topic;
        return $this;
    }

    private function getAccount() {
        $accountModel = new AccountModel();

        $account = $accountModel->get( $this->data['post']['account_id'], [ 'id', 'uname' ] );

        if( !$account ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data['post']['account'] = $account;
        return $this;
    }

    private function getPost() {
        $post_id = $this->params[ 'post_id' ];

        $postDataModel = new PostDataModel();

        $post_data = $postDataModel->select( [
            'where' => [
                ['id', $post_id],
                ['status', 0]
            ]
        ] );

        if( !$post_data || !count( $post_data ) ) {
            $this->error( 'POST_NOTEXISTS' );
        }

        $post_data = $post_data[0];

        $postModel = new PostModel();

        $post = $postModel->get( $post_data[ 'id' ] );

        $post['data'] = $post_data;

        if( $this->account && $this->account['id'] == $post['account_id'] ) {
            $post[ 'own' ] = 1;
        } else {
            $post['own'] = 0;
        }

        $this->data['post'] = $post;

        return $this;
    }

    private function paramsProcessing() {
        $post_id = $this->request->getQuery( 'post_id' );

        if( is_null( $post_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = [
            'post_id' => $post_id
        ];

        return $this;
    }
}
