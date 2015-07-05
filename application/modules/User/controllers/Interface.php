<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {

    public function init() {
        parent::init();

        $this->actions = array(
            'userposts' => $this->action_path . 'UserPosts.php'
        );
    }
}
