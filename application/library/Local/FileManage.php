<?php

namespace Local;

require dirname( __FILE__ ) . '/OSS/sdk.class.php';

Class FileManage {
    static public function ossservice() {
        $conf = self::conf(); 

        $ossservice = new \ALIOSS(
            $conf->keyid,
            $conf->keysecret,
            $conf->host
        );

        if( !$ossservice ) {
            return false;
        }

        return $ossservice;
    }

    static public function conf() {
        return \Local\Utils::loadConf( 'oss', 'idizcuz' );
    }

    static public function saveAvatar( $avatar = null ) {
        $conf = self::conf( 'attachment', 'posts' ); 

        $response = self::ossservice()->upload_file_by_file(
            $conf->avatar,
            'yangliang.png',
            '/Users/lvchengbin/Projects/iDizcuz/application/library/Local/OSS/yl.png'
        );

        var_dump( $response );
    }

    static public function savePoststc( $filename, $path ) {
        $conf = self::conf();

        $response = self::ossservice()->upload_file_by_file(
            $conf->poststc,
            $filename,
            $path
        );

        return $response;
    }

    static public function saveTopicstc( $filename, $path ) {
        $conf = self::conf();

        $response = self::ossservice()->upload_file_by_file(
            $conf->topicstc,
            $filename,
            $path
        );
        return $response;
    }
}
