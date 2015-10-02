<?php

Class ListAction extends \Local\BaseAction {
    private $data = [];
    private $rn = 40;

    public function __execute() {
        $this->tpl = 'article/list';
        $this->paramsProcessing()->getTopic();
        return $this->data;
    }

    public function __mobile() {
        $this->tpl = 'articleMobile/list';
        $this->paramsProcessing()->getTopic();
        return $this->data;
    }

    private function getTopic() {
        $id = $this->params['id'];
        $topicDataModel = new TopicDataModel();

        $topic_data = $topicDataModel->get( $id, ['id', 'status'] );

        if( !$topic_data || $topic_data['status'] != 1 ) {
            // 404
        }

        $topicModel = new TopicModel();

        $topic = $topicModel->get( $id, ['id','title'] );

        $topic['data'] = $topic_data;

        $this->data['topic'] = $topic;
        
        return $this;
    }

    private function paramsProcessing() {
        $id = $this->__getParam( 'id' );

        $pn = intval( $this->__getParam( 'pn' ) );

        if( $pn < 1 ) $pn = 1;

        $start = ( $pn - 1 ) * $this->rn;

        $this->params = [
            'id' => $id,
            'start' => $start
        ];

        return $this;
    }
}
