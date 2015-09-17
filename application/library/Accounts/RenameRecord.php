<?php

namespace Accounts;

Class RenameRecord {
    static private $accountRenameModel;

    static private function getAccountRenameModel() {
        if( self::$accountRenameModel ) return self::$accountRenameModel;
        self::$accountRenameModel = new \AccountRenameModel();
        return self::$accountRenameModel;
    }

    static public function getRenameRecord( $id, $columns = null ) {
        return self::getAccountRenameModel()->get( $id, $columns );
    }

    static public function renameable( $id ) {
        $res = self::getAccountRenameModel()->select( [
            'where' => [
                ['account_id', $id]
            ],
            'order' => [
                ['id','DESC']
            ]
        ] );

        if( !$res ) return [
            'renameable' => true
        ];

        $res = $res[0];
        
        $res['renameable'] = false;

        if( $res['status'] == 2 ) {
            $res['renameable'] = true;
            return $res;
        }

        $dis = $_SERVER['REQUEST_TIME'] - strtotime( $res['ctime'] );
        $frozen = 3600 * 24 * 30;

        if( $dis > $frozen ) {
            $res['renameable'] = true;
            return $res;
        }

        $res['dis'] = ( $frozen -  $dis );

        return $res;
    }

    static public function pass( $id ) {
        $res = self::getAccountRenameModel()->update( [
            'set' => [
                'status' => 1
            ],
            'where' => [
                [ 'id', $id ]
            ]
        ] );

        return $res;
    }

    static public function nopass( $id ) {
        $res = self::getAccountRenameModel()->update( [
            'set' => [
                'status' => 2
            ],
            'where' => [
                ['id', $id]
            ]
        ] );
        return $res;
    }
}
