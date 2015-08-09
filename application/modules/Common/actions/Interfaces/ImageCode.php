<?php

Class ImageCodeAction extends \Local\BaseAction {
    private $code;
    private $image;

    public function __execute() {
        $this->type = 'interface';

        header( 'Content-Type:image/gif;' );

        $this->paramsProcessing()->createCode()->createImage();

        imagegif( $this->image );

        exit;
    }

    private function createImage() {
        $params = $this->params;

        $width = $params[ 'width' ];
        $height = $params[ 'height' ];
        $len = $params[ 'len' ];

        $im = imagecreatetruecolor( $width, $height );

        $bgColor = imagecolorallocate( $im, 255, 255, 255 );
        imagefill( $im, 0, 0, $bgColor);

        for( $i = 0; $i <= 20; ++$i) {
            $pixelColor = imagecolorallocate(
                $im,
                mt_rand( 0, 255 ),
                mt_rand( 0, 255 ),
                mt_rand( 0, 255 )
            );

            imagesetpixel(
                $im,
                mt_rand( 0, $width ), mt_rand( 0, $height ),
                $pixelColor
            );
        }

        for( $i = 0; $i < $len; ++$i ) {
            $r = mt_rand( 0, 255 );
            $g = mt_rand( 0, 255 );
            $b = mt_rand( 0, 255 );
            $color = imagecolorallocate( $im, $r, $g, $b );

            $fontSize = mt_rand( $width / $len - 14, $width / $len - 9 );
            $x = floor( ( $width - $len ) / $len ) * $i + 5;
            $y = ( $height - $fontSize + 35 ) / 2;

            $c = $this->code{$i};

            imagettftext( $im, $fontSize + 10, mt_rand( -30, 30 ), $x, $y, imagecolorallocatealpha( $im, $r, $g, $b, 100 ), APP_PATH . '/resources/BodoniOrnaments.ttf', $c );

            $angle = mt_rand( -30, 30 );

            if( $c === 1 || $c === 0 ) {
                imagettftext( $im, $fontSize + 2, $angle + 1, $x, $y, imagecolorallocate( $im, 0, 0, 0 ), APP_PATH . '/resources/AndaleMono.ttf', $c );
                imagettftext( $im, $fontSize, $angle, $x, $y, $color, APP_PATH . '/resources/AndaleMono.ttf', $c );
            } else {
                imagettftext( $im, $fontSize + 2, $angle + 1, $x, $y, imagecolorallocate( $im, 0, 0, 0 ), APP_PATH . '/resources/AmericanTypewriter.ttc', $c );
                imagettftext( $im, $fontSize, $angle, $x, $y, $color, APP_PATH . '/resources/AmericanTypewriter.ttc', $c );
            }
        }

        $this->image = $im;
    }

    private function createCode() {
        $code = \Local\Utils::randomString( $this->params['len'] );
        $this->code = $this->session[ 'imagecode' ] = $code;
        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $width = intval( $request->getQuery( 'width' ) );

        if( $width == 0 ) {
            $width = 120;
        }

        $height = intval( $request->getQuery( 'height' ) );

        if( $height == 0 ) {
            $height = 40;
        }

        $len = intval( $request->getQuery( 'len' ) );

        if( $len == 0 ) {
            $len = 4;
        }

        $this->params = [
            'width' => $width,
            'height' => $height,
            'len' => $len
        ];

        return $this;

    }
}
