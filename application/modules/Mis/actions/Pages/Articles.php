<?php

Class ArticlesAction extends \Local\MisAction {
    private $data = [];
    private $topicEventModel;

    public function __execute() {
        $this->tpl = 'mis/articles';

        $this->paramsProcessing();

        $this->topicEventModel = new TopicEventModel();

        $this->getEvents();

        return $this->data;
    }

    private function getEvents() {
        $events = $this->topicEventModel->getEvents();

        $this->data[ 'events' ] = $events;
        return $this;
    }

    private function paramsProcessing() {
        return $this;
    }
}
