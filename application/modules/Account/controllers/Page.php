<?php

use Yaf\Registry;

Class PageController extends BasePageController {

    public function init() {
        parent::init();

        $this->actions = array(
            'signup' => $this->action_path . 'Signup.php',
            'signin' => $this->action_path . 'Signin.php',
            'forget' => $this->action_path . 'Forget.php',
            'signout' => $this->action_path . 'Signout.php'
        );
    }
}
