<?php

use Yaf\Registry;

Class PageController extends BasePageController {

    public function init() {
        parent::init();

        $this->actions = array(
            'list' => $this->action_path . 'List.php',
            'article' => $this->action_path . 'Article.php'
        );
    }
}
