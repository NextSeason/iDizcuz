<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {

    public function init() {
        parent::init();

        $this->action = array(
            'user' => $this->action_path . 'User.php'
        );
    }
}
