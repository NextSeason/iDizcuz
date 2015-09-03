<?php

namespace User;

Class Follow {
    static private $followModel;

    static private function getFollowModel() {
        if( self::$followModel ) return self::$followModel;
        self::$followModel = new \FollowModel();
        return self::$followModel;
    }

    static public function check( $account_id, $fans_id ) {
        $followModel = self::getFollowModel();
        $follow = $followModel->select( [
            'columns' => [ 'id' ],
            'where' => [
                [ 'account_id', $account_id ],
                [ 'fans_id', $fans_id ]
            ]
        ] );

        return count( $follow ) ? true :  false;
    }
}
