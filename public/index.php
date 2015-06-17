<?php
    
    define( 'APP_PATH', realpath( dirname(__FILE__) . '/../' ) );
    define( 'TPL_PATH', APP_PATH . '/application/views/page/' );

    $app = new \Yaf\Application( APP_PATH . '/conf/application.ini' );
    $app->bootstrap()->run();
