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
            $color = imagecolorallocate(
                $im, mt_rand( 0, 255 ), mt_rand( 0, 255 ), mt_rand( 0, 255 )
            );

            $fontSize = mt_rand( $width / $len - 14, $width / $len - 9 );
            $x = floor( ( $width - $len ) / $len ) * $i + 5;
            $y = ( $height - $fontSize + 30 ) / 2;
            imagettftext( $im, $fontSize, mt_rand( -30, 30 ), $x, $y, $color, APP_PATH . '/resources/Noteworthy.ttc', $this->code{$i} );
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
