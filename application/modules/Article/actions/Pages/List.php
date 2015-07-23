<?php

Class ListAction extends \Local\BaseAction {
    private $data = [];

    public function __execute() {
        $this->tpl = 'article/list';

        $this->paramsProcessing()->getTopic();

        return $this->data;
    }

    private function getTopic() {
        $id = $this->params['id'];
        $topicDataModel = new TopicDataModel();

        $topic_data = $topicDataModel->get( $id );

        if( !$topic_data || $topic_data['status'] != 1 ) {
            // 404
        }

        $topicModel = new TopicModel();

        $topic = $topicModel->get( $id );

        $topic['data'] = $topic_data;

        $this->data['topic'] = $topic;
        
        return $this;
    }

    private function paramsProcessing() {
        $id = $this->request->getParam( 'id' );

        $this->params = [
            'id' => $id
        ];

        return $this;
    }
}
