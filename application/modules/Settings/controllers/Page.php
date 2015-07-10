<?php

use Yaf\Registry;

Class PageController extends BasePageController {
    public function init() {
        parent::init();

        $this->actions = array(
            'info' => $this->action_path . 'Info.php',
            'passwd' => $this->action_path . 'Passwd.php',
            'notification' => $this->action_path . 'Notification.php',
            'mail' => $this->action_path . 'Mail.php'
        );
    }
}
