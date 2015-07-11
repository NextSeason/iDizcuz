<?php

Class UploadAction extends \Local\MisAction {
    private $data = [];

    private $mime2Ext = array(
        'image/jpeg' => '.jpg',
        'image/gif' => '.gif',
        'image/bmp' => '.bmp',
        'image/png' => '.png'
    );

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->check()->save();

        return $this->data;
    }

    private function save() {
        $image = $this->params[ 'file' ];

        if( is_uploaded_file( $image[ 'tmp_name' ] ) ) {

            $md5 = md5_file( $image['tmp_name'] );

            $filename = $md5 . $this->mime2Ext[ $image['type'] ];


            $response = \Local\FileManage::saveTopicstc( $filename, $image[ 'tmp_name' ] );

            if( $response->status != 200 ) {
                $this->error( 'SYSTEM_ERR' );
            }

            $conf = \Local\Utils::loadConf( 'attachment', 'topics_and_events' );

            $this->data[ 'url' ] = $filename;

        } else {
            $this->error( 'SYSTEM_ERR' );
        }
 
    }

    private function check() {
        $image = $this->params[ 'file' ];
        $conf = \Local\Utils::loadConf( 'attachment', 'topics_and_events' );

        if( $image['size'] > $conf->maxsize ) {
            $this->error( 'EXCEEDED_MAX' );
        }

        $mimes = explode( '|', $conf->allowmime );

        if( !in_array( $image['type'], $mimes ) ) {
            $this->error( 'UNSUPPORT_MIME' );
        }
        return $this;

    }

    private function paramsProcessing() {
        $file = $this->request->getFiles( 'file' );

        if( is_null( $file ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params[ 'file' ] = $file;

        return $this;
    }
}
