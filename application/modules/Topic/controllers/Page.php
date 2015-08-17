<?php

use Yaf\Registry;

Class PageController extends BasePageController {

    public function init() {
        parent::init();

        $this->actions = array(
            'topic' => $this->action_path . 'Topic.php',
            'post' => $this->action_path . 'Post.php',
            'list' => $this->action_path . 'List.php',
            'write' => $this->action_path . 'Write.php'
        );
    }
}
