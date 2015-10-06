<?php

namespace Local;

require dirname( __FILE__ ) . '/HTMLPurifier/HTMLPurifier.auto.php';

Class EditorPurifier {
    static public function purifyPost( $content ) {

        $config = \HTMLPurifier_Config::createDefault();

        $config->set( 'HTML.Allowed', 'p,b,i,u,ul,ol,li,a[href],img[src],strong,em,span' );
        $purifier = new \HTMLPurifier( $config );
        $clean_html = $purifier->purify( $content );

        return $clean_html;
    }

    static public function purifyComment( $content ) {
        $config = \HTMLPurifier_Config::createDefault();

        $config->set( 'HTML.Allowed', '' );
        $purifier = new \HTMLPurifier( $config );
        $clean_html = $purifier->purify( $content );

        return $clean_html;
    }
}
