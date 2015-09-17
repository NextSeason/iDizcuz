<?php

namespace Local;

Class Validate {
    static public function email( $email ) {
        if( is_null( $email ) ) return false;
        return filter_var( $email, FILTER_VALIDATE_EMAIL );
    }

    static public function uname( $uname ) {
        if( is_null( $uname ) ) return false;
        $len = mb_strlen( $uname );

        if( $len == 0 || $len > 50 ) {
            return false;
        }
        return true;
    }

    static public function passwd( $passwd ) {
        if( is_null( $passwd ) ) return false;
        $len = strlen( $passwd );

        if( $len < 6 || $len > 20 ) {
            return false;
        }
        return true;
    }
}
