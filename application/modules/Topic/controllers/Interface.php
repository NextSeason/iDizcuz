<?php

use Yaf\Registry;

Class InterfaceController extends BaseInterfaceController {
    public function init() {
        parent::init();

        $action_path = sprintf( 'modules/%s/actions/Interfaces/', $this->_module );

        $this->actions = array(
            'public' => $action_path . 'Public.php'
        );
    }
}
