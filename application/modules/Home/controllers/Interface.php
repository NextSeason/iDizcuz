<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {

    public function init() {
        parent::init();

        $this->actions = array(
            'hotposts' => $this->action_path . 'HotPosts.php',
            'topicpointsdata' => $this->action_path . 'TopicPointsData.php',
            'latestposts' => $this->action_path . 'LatestPosts.php',
            'posts' => $this->action_path . 'Posts.php',
            'accounts' => $this->action_path . 'Accounts.php'
        );
    }
}
