<?php

use Yaf\Registry;

Class PageController extends BasePageController {
    public function init() {
        parent::init();

        $this->actions = array(
            'info' => $this->action_path . 'Info.php'
        );
    }
}
