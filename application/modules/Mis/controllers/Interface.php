<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {
    public function init() {
        parent::init();

        $this->actions = array(
            'newtopic' => $this->action_path . 'NewTopic.php'
        );
    }
}
