<?php

use Yaf\Registry;

Class PageController extends BasePageController {

    public function init() {
        parent::init();

        $this->actions = array(
            'imagecode' => $this->action_path . 'ImageCode.php'
        );
    }
}
