<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {

    public function init() {
        parent::init();

        $action_path = sprintf( 'modules/%s/actions/Interfaces/', $this->_module );

        $this->actions = array(
            'sendvcode' => $action_path . 'SendVcode.php',
            'signup' => $action_path . 'Signup.php',
            'signin' => $action_path . 'Signin.php',
        );
    }
}
