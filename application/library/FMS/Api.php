<?php

namespace FMS;

Class Api {
    static private $FMSModel;

    static private function getFMSModel() {
        if( self::$FMSModel ) return self::$FMSModel;
        self::$FMSModel = new \FMSModel();
        return self::$FMSModel;
    }

    static public function getFragmentByAlias( $alias, $decode = true ) {
        $fms = self::getFMSModel()->select( [
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
        return self::getFMSModel()->get( $id );
    }
}
