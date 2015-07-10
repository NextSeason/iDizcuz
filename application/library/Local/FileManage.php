<?php

namespace Local;

require dirname( __FILE__ ) . '/OSS/sdk.class.php';

Class FileManage {
    static public function saveAvatar( $avatar = null ) {
        $conf = \Local\Utils::loadConf( 'oss', 'avatar' );

        $ossservice = new \ALIOSS(
            $conf->access->key_id, 
            $conf->access->key_secret,
            $conf->host
        );

        $response = $ossservice->upload_file_by_file(
            $conf->bucket,
            'yangliang.png',
            '/Users/lvchengbin/Projects/iDizcuz/application/library/Local/OSS/yl.png'
        );

        var_dump( $response );
    }
}
