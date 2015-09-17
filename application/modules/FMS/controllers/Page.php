<?php

use Yaf\Registry;

Class PageController extends BasePageController {
    public function init() {
        parent::init();

        $this->actions = array(
            'fms' => $this->action_path . 'FMS.php',
        );
    }
}
