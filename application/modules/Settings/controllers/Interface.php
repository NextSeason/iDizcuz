<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {

    public function init() {
        parent::init();

        $this->actions = array(
            'updateinfo' => $this->action_path . 'UpdateInfo.php',
            'passwd' => $this->action_path . 'Passwd.php',
            'uploadavatar' => $this->action_path . 'UploadAvatar.php'
        );
    }
}
