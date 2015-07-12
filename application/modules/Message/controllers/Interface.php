<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {

    public function init() {
        parent::init();

        $this->actions = array(
            'getmessages' => $this->action_path . 'GetMessages.php',
            'read' => $this->action_path . 'Read.php',
            'remove' => $this->action_path . 'Remove.php'
        );
    }
}
