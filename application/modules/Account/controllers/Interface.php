<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {

    public function init() {
        parent::init();

        $this->actions = array(
            'sendvcode' => $this->action_path . 'SendVcode.php',
            'signup' => $this->action_path . 'Signup.php',
            'signin' => $this->action_path . 'Signin.php',
            'forget' => $this->action_path . 'Forget.php',
            'forgetreset' => $this->action_path . 'ForgetReset.php'
        );
    }
}
