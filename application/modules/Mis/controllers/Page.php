<?php

use Yaf\Registry;

Class PageController extends BasePageController {
    public function init() {
        parent::init();

        $this->actions = array(
            'signin' => $this->action_path . 'Signin.php',
            'index' => $this->action_path . 'Index.php',
            'newtopic' => $this->action_path . 'NewTopic.php',
            'newevent' => $this->action_path . 'NewEvent.php'
        );
    }
}
