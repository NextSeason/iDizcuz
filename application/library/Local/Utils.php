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

    static public function pickByKey( $arr, $key = 'id', $unique = true ) {
        $res = [];
        foreach( $arr as $v ) $res[] = $v[ $key ];
        return $unique ? array_unique( $res ) : $res;
    }

    static public function gather( $arr, $key = 'id' ) {
        $res = [];
        foreach( $arr as $v ) $res[$v[$key]] = $v;
        return $res;
    }

    static public function encodeId( $id ) {
        $id = (int)$id;
        if( $id === 0 ) return 0;
        return $id ^ 0x11080509;  
    }

    static public function decodeId( $sid ) {
        $sid = (int)$sid;
        if( $sid === 0 ) return 0;
        return $sid ^ 0x11080509;  
    }

    static function traverseEncodeId( $arr ) {
        foreach( $arr as $k => &$v ) {
            if( is_array( $v ) ) {
                $v = Utils::traverseEncodeId( $v );
            } else {
                if( $k === 'id' || preg_match( '#_id$#', $k ) ) {
                    $v = Utils::encodeId( $v );
                }
            }
        }
        return $arr;
    }

    static function traverseDecodeId( $arr ) {
        foreach( $arr as $k => &$v ) {
            if( is_array( $v ) ) {
                $v = Utils::traverseDecodeId( $v );
            } else {
                if( $k === 'id' || preg_match( '#_id$#', $k ) ) {
                    $v = Utils::decodeId( $v );
                }
            }
        }
        return $arr;
    }

    static function str2timestamp( $str ) {
        // today, this week, this month, this year
        switch( $str ) {
            case 'this week' :
                break;
            case 'this month' :
                return date( 'Y-m', $_SERVER['REQUEST_TIME'] );
                break;
            case 'this year' :
                return date( 'Y', $_SERVER['REQUEST_TIME'] );
                break;
            case 'today' :
            default :
                return date( 'Y-m-d', $_SERVER['REQUEST_TIME'] );
                break;
        }
    }

    static function ntocn( $n ) {
        $cns = [ '一', '二', '三', '四', '五', '六', '七', '八', '九', '十' ];
        return $cns[$n-1];
    }
}
