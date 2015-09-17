<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {
    public function init() {
        parent::init();

        $this->actions = array(
            'signin' => $this->action_path . 'Signin.php',
            'topic' => $this->action_path . 'Topic.php',
            'article' => $this->action_path . 'Article.php',
            'upload' => $this->action_path . 'Upload.php',
            'publictopic' => $this->action_path . 'PublicTopic.php',
            'point' => $this->action_path . 'Point.php',
            'editoruploadimages' => $this->action_path . 'EditorUploadImages.php',
            'deletepost' => $this->action_path . 'DeletePost.php',
            'banuser' => $this->action_path . 'BanUser.php',
            'unbanuser' => $this->action_path . 'UnbanUser.php',
            'dealrename' => $this->action_path . 'DealRename.php'
        );
    }
}
