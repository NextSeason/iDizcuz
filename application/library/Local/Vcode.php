<?php

namespace Local;

Class Vcode {
    static public function save( $code, $props ) {
        $session = \Yaf\Session::getInstance();

        $codes = empty( $session[ 'vcodes' ] ) ? array() : $session[ 'vcodes' ];        
                                                                                                      
         /**                                                                                          
          * I don't think it is necessary to do check duplication
          * because it is little probability event which create two or more same vcode from randomString method.
          * and if it happens, it will not make bad experince
          */
         $props[ 'time' ] = $_SERVER[ 'REQUEST_TIME' ];
         $codes[ $code ] = $props;
         $session[ 'vcodes' ] = $codes;
    }

    static public function check( $code, $lifetime, $props, $destory = true ) {
        $session = \Yaf\Session::getInstance();

        $codes = $session[ 'vcodes' ];

        if( empty( $codes[ $code ] ) ) return false;

        $target = $codes[ $code ];

        if( $_SERVER[ 'REQUEST_TIME' ] - $target[ 'time' ] > $lifetime ) return false;

        foreach( $props as $key => $value ) {
            if( empty( $target[ $key ] ) || $target[ $key ] != $value ) return false;
        }

        if( $destory ) {
            $codes[ $code ] = null;
            unset( $codes[ $code ] );
            $session[ 'vcodes' ] = $codes;
        }

        return true;

    } 
}
