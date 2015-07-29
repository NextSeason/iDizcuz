<?php

namespace Local;

Class Utils {
    /**
     * randomString 
     *
     * param $length - length of string need to create
     * param $type - what type of charactars in the string, 0 means both numbers and letters, 1 means numbers only and 2 means letters only
     */
    static public function randomString( $len, $type = 0 ) {
        $result = '';

        /**
         * the string length must greator then 0,
         * or a warning will be thrown by array_rand
         */
        if( !$len ) return '';

        switch( $type ) {
            case 1 :
                $optional = range( 0, 9 );
                break;

            case 2 :
                $optional = array_merge( range( 'a', 'z' ), range( 'A', 'Z' ) );
                break;

            case 0 :
            default :
                $optional = array_merge( range( 'a', 'z' ), range( 'A', 'Z' ), range( 0, 9 ) );
                break;
        }

        $max = count( $optional ) - 1;

        for( $i = 0; $i < $len; ++$i ) {

            $result .= $optional[ rand( 0, $max ) ];
        }

        return $result;
    }

    /**
     * @method loadConf
     * @desc load configuration file 
     *
     * @param $conf - configuration file name
     * @param $tag - tag in configuration file
     * @return content of configuration
     */
    static public function loadConf( $conf, $tag ) {
        $host = $_SERVER[ 'HTTP_HOST' ];
        $path = '/conf/';

        if( $host != 'www.idizcuz.com' ) {
            $path = '/local-conf/';
        }

        return new \Yaf\Config\Ini( APP_PATH . $path . $conf . '.ini', $tag );
    }

    static public function passwd( $passwd, $salt ) {
        return sha1( $passwd . $salt );
    }

    static public function redirect( $path ) {
        header( 'Location:' . $path );
        exit;
    }

    static public function pointIndex( $params ) {
        $index = $params['post_cnt'] * 10 + floor( $params['agree'] / 20 ) - floor( $params['disagree'] / 20 );
        return max( $index, 0 );
    }
}
