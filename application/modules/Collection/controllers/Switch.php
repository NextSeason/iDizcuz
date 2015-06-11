<?php

use Yaf\Registry;

Class SwitchController extends \Yaf\Controller_Abstract {
    public function entranceAction() {

        $this->forward( 'user', 'index', 'index' );

        print_r( Registry::get( 'xxx' ) );

        echo 'Success!';
        return false;
    }
}
