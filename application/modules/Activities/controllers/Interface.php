<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {

    public function init() {
        parent::init();

        $this->actions = array(
            'getactivities' => $this->action_path . 'GetActivities.php'
        );
    }
}
