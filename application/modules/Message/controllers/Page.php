<?php

use Yaf\Registry;

Class PageController extends BasePageController {

    public function init() {
        parent::init();

        $this->actions = array(
            'message' => $this->action_path . 'Message.php'
        );
    }
}
