<?php

Class NextSeason {
    public function __construct() {
    }

    static public function call( $module, $api ) {

        $loader = \Yaf\Loader::getInstance();
        $loader->import( "../modules/$module/api/$module.php" );

        $class = $module . 'Api';

        return $class::$api();
    }
}
