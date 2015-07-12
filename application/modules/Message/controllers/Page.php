<?php

use Yaf\Registry;

Class PageController extends BasePageController {

    public function init() {
        parent::init();

        $this->actions = array(
            'messages' => $this->action_path . 'Messages.php'
        );
    }
}
