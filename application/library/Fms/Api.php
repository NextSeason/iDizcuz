<?php

namespace Fms;

Class Api {
    static private $FmsModel;

    static private function getFmsModel() {
        if( self::$FmsModel ) return self::$FmsModel;
        self::$FmsModel = new \FmsModel();
        return self::$FmsModel;
    }

    static public function getFragmentByAlias( $alias, $decode = true ) {
        $fms = self::getFmsModel()->select( [
            'where' => [
                [ 'alias', $alias ]
            ]
        ] );

        if( !$fms ) return $fms;

        $fms = $fms[0];

        if( $decode ) $fms['content'] = json_decode( $fms['content'], true );

        return $fms;
    }

    static public function get( $id ) {
        return self::getFmsModel()->get( $id );
    }
}
