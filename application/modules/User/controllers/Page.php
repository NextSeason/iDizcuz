<?php

use Yaf\Registry;

Class PageController extends BasePageController {
    
    public function init() {
        parent::init();

        $this->actions = array(
            'home' => $this->action_path . 'Home.php'
        );
    }
}
