<?php

use Yaf\Registry;

Class IndexController extends BaseController {

    public $actions = array();

    public function init() {
        parent::init();
        $this->actions = array(
            'index' => sprintf( 'modules/%s/actions/Index.php', $this->_module )
        );
    }
}
