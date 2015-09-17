<?php

namespace Accounts;

Class Api {
    static private $accountModel;
    static private $accountDataModel;
    static private $accountSettingModel;

    static private function getAccountModel() {
        if( self::$accountModel ) return self::$accountModel;
        self::$accountModel = new \AccountModel();
        return self::$accountModel;
    }

    static private function getAccountDataModel() {
        if( self::$accountDataModel ) return self::$accountDataModel;
        self::$accountDataModel = new \AccountDataModel();
        return self::$accountDataModel;
    }

    static private function getAccountSettingModel() {
        if( self::$accountSettingModel ) return self::$accountSettingModel;
        self::$accountSettingModel = new \AccountSettingsModel();
        return self::$accountSettingModel;
    }

    static public function get( $id, $columns = null ) {
        return self::getAccountModel()->get( $id, $columns );
    }

    static public function getData( $id, $columns = null ) {
        return self::getAccountDataModel()->get( $id, $columns );

    }
    static public function getSettings( $id, $columns = null ) {
        return self::getAccountSettingModel()->get( $id, $columns );
    }

    static public function ban( $id, $start, $end ) {
        return self::getAccountModel()->update( [
            'set' => [
                'ban_start' => $start,
                'ban_end' => $end,
                'status' => 1
            ],
            'where' => [
                ['id', $id]
            ]
        ] );
    }

    static public function unban( $id ) {
        return self::getAccountModel()->update( [
            'set' => [
                'status' => 0
            ],
            'where' => [
                ['id', $id]
            ]
        ] );
    }

    static public function rename( $id, $uname ) {
        $res = self::getAccountModel()->update( [
            'set' => [
                'uname' => $uname,
            ],
            'where' => [
                ['id', $id]
            ]
        ] );
        return $res;
    }
}
