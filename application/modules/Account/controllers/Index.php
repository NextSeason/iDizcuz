<?php

use Yaf\Registry;

Class IndexController extends BaseController {

    public function init() {
        parent::init();

        $this->actions = array(
            'signup' => sprintf( 'modules/%s/actions/Signup.php', $this->_module ),
            'vcode' => sprintf( 'modules/%s/actions/Vcode.php', $this->_module )
        );
    }
}
