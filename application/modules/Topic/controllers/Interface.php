<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {
    public function init() {
        parent::init();

        $this->actions = array(
            'post' => $this->action_path . 'Post.php',
            'getpost' => $this->action_path . 'GetPost.php',
            'getposts' => $this->action_path . 'GetPosts.php',
            'mark' => $this->action_path . 'Mark.php',
            'vote' => $this->action_path . 'Vote.php',
            'report' => $this->action_path . 'Report.php',
            'getevents' => $this->action_path . 'GetEvents.php'
        );
    }
}
