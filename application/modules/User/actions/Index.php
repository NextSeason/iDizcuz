<?php

Class IndexAction extends \Yaf\Action_Abstract {
    public function execute() {
        echo 'abc';
        echo NextSeason::call( 'Account', 'test' );
    }
}
