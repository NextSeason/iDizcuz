<?php

use Yaf\Registry;

Class PageController extends BasePageController {

    public function init() {
        parent::init();

        $this->actions = array(
            'topic' => $this->action_path . 'Topic.php'
        );
    }
}
