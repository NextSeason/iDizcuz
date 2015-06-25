<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {
    public function init() {
        parent::init();

        $action_path = sprintf( 'modules/%s/actions/Interfaces/', $this->_module );

        $this->actions = array(
            'post' => $action_path . 'Post.php',
            'getposts' => $action_path . 'GetPosts.php',
            'mark' => $action_path . 'Mark.php',
            'vote' => $action_path . 'Vote.php',
            'report' => $action_path . 'Report.php'
        );
    }
}
