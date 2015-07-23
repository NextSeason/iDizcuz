<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {
    public function init() {
        parent::init();

        $this->actions = array(
            'topic' => $this->action_path . 'Topic.php',
            'article' => $this->action_path . 'Article.php',
            'upload' => $this->action_path . 'Upload.php',
            'publictopic' => $this->action_path . 'PublicTopic.php',
            'point' => $this->action_path . 'Point.php'
        );
    }
}
