<?php

use Yaf\Registry;

Class PageController extends BasePageController {

    public function init() {
        parent::init();

        $this->actions = array(
            'index' => $this->action_path . 'Index.php'
        );
    }
}
