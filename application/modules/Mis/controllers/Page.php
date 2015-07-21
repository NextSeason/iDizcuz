<?php

use Yaf\Registry;

Class PageController extends BasePageController {
    public function init() {
        parent::init();

        $this->actions = array(
            'signin' => $this->action_path . 'Signin.php',
            'index' => $this->action_path . 'Index.php',
            'topic' => $this->action_path . 'Topic.php',
            'topiclist' => $this->action_path . 'TopicList.php',
            'newevent' => $this->action_path . 'NewEvent.php',
            'eventlist' => $this->action_path . 'EventList.php'
        );
    }
}
