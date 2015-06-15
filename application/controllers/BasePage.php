<?php

Class BasePageController extends BaseController {
    public function init() {
        parent::init();

        $this->setViewpath( APP_PATH . '/application/views' );
    }
}
