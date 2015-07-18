<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {

    public function init() {
        parent::init();

        $this->actions = array(
            'userposts' => $this->action_path . 'UserPosts.php',
            'votedposts' => $this->action_path . 'VotedPosts.php',
            'markedposts' => $this->action_path . 'MarkedPosts.php',
            'removedposts' => $this->action_path . 'RemovedPosts.php',
            'saveavatar' => $this->action_path . 'SaveAvatar.php',
            'follow' => $this->action_path . 'Follow.php',
            'unfollow' => $this->action_path . 'Unfollow.php',
            'getfollows' => $this->action_path . 'GetFollows.php',
            'getfans' => $this->action_path . 'GetFans.php'
        );
    }
}
