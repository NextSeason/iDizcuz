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

    static public function get( $props ) {
        $session = \Yaf\Session::getInstance();

        if( empty( $session['vcode'] ) ) return null;

        $codes = $session['vcodes'];

        $result = [];

        foreach( $codes as $code ) {
            foreach( $props as $key => $prop ) {
                if( !isset( $code[$key] ) || $code[$key] != $prop ) {
                    break;
                }
                $result[] = $code;
            }
        }

        return $result;
    }

    static public function check( $code, $lifetime, $props, $destory = true ) {
        $session = \Yaf\Session::getInstance();

        $times = isset( $session[ 'vcode_failed_times' ] ) ? $session['vcode_failed_times'] : 0;

        $codes = $session[ 'vcodes' ];

        if( empty( $codes[ $code ] ) ) {
            $times++;

            if( $times > 10 ) {
                $session['vcodes'] = [];
                $session['vcode_failed_times'] = 0;

                return 'FAIL_MAXTIMES';
            }

            $session['vcode_failed_times'] = $times;

            return 'VCODE_ERR';
        }

        $target = $codes[ $code ];

        if( $_SERVER[ 'REQUEST_TIME' ] - $target[ 'time' ] > $lifetime ) {

            $codes[$code] = null;
            unset( $codes[$code] );

            $session['vcodes'] = $codes;
            return 'VCODE_ERR';
        }

        foreach( $props as $key => $value ) {
            if( empty( $target[ $key ] ) || $target[ $key ] != $value ) return 'VCODE_ERR';
        }

        if( $destory ) {
            $codes[ $code ] = null;
            unset( $codes[ $code ] );
            $session[ 'vcodes' ] = $codes;
        }

        // if success set failed record to 0
        $session['vcode_failed_times'] = 0;

        return true;
    } 

    static public function remove( $props ) {
        $session = \Yaf\Session::getInstance();

        $codes = $session[ 'vcodes' ];

        if( !count( $codes ) ) return;

        $s = true;

        foreach( $codes as $k => $code ) {
            $s = true;
            foreach( $props as $key => $prop ) {
                if( $code[$key] != $prop ) {
                    $s = false;
                    break;
                }
            }
            $codes[ $k ] = null;
            unset( $codes[$k] );
        }

        $session[ 'vcodes' ] = $codes;
    }
}
