<?php

use Yaf\Registry;

Class PageController extends BasePageController {
    public function init() {
        parent::init();

        $this->actions = array(
            'signin' => $this->action_path . 'Signin.php',
            'index' => $this->action_path . 'Index.php',
            'topic' => $this->action_path . 'Topic.php',
            'topics' => $this->action_path . 'Topics.php',
            'article' => $this->action_path . 'Article.php',
            'articles' => $this->action_path . 'Articles.php',
            'reports' => $this->action_path . 'Reports.php',
            'report' => $this->action_path . 'Report.php',
            'post' => $this->action_path . 'Post.php',
            'user' => $this->action_path . 'User.php',
            'bannedusers' => $this->action_path . 'BannedUsers.php',
            'renamerecords' => $this->action_path . 'RenameRecords.php'
        );
    }
}
