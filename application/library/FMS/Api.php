<?php

namespace FMS;

Class Api {
    static private $FMSModel;

    static private function getFMSModel() {
        if( self::$FMSModel ) return self::$FMSModel;
        self::$FMSModel = new \FMSModel();
        return self::$FMSModel;
    }

    static public function getFragmentByAlias( $alias ) {
        $fms = self::getFMSModel()->select( [
            'where' => [
                [ 'alias', $alias ]
            ]
        ] );
        return $fms ? $fms[0] : $fms;
    }

    static public function get( $id ) {
        return self::getFMSModel()->get( $id );
    }
}
