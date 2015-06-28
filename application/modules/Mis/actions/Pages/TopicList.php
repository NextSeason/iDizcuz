<?php

Class TopicListAction extends \Local\MisAction {
    private $data = [];
    private $topicModel;

    public function __execute() {
        $this->tpl = 'mis/topicList';

        $this->paramsProcessing();

        $this->topicModel = new TopicModel();

        $this->getTopics();

        return $this->data;
    }

    private function getTopics() {
        $topics = $this->topicModel->getTopics();
        $this->data[ 'topics' ] = $topics;
        return $this;
    }

    private function paramsProcessing() {
        return $this;
    }
}
