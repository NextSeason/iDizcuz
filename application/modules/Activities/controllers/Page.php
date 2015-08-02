<?php

use Yaf\Registry;

Class PageController extends BasePageController {

    public function init() {
        parent::init();

        $this->actions = array(
            'activities' => $this->action_path . 'Activities.php'
        );
    }
}
