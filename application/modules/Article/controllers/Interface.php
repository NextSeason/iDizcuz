<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {

    public function init() {
        parent::init();

        $this->actions = [
            'topicarticles' => $this->action_path . 'TopicArticles.php'
        ];
    }
}
