<?php

Class UploadAvatarAction extends \Local\BaseAction {
    private $data = [];

    private $mime2Ext = array(
        'image/jpeg' => '.jpg',
        'image/gif' => '.gif',
        'image/bmp' => '.bmp',
        'image/png' => '.png'
    );



    public function __execute() {
        $this->type = 'interface';

        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        } 

        $this->paramsProcessing()->check()->save()->update();


        return $this->data;
    }

    private function update() {
        $accountModel = new AccountModel();

        $res = $accountModel->update( [
            'set' => [ 'img' => $this->data['img'] ],
            'where' => [
                [ 'id', $this->account['id'] ]
            ]
        ] );

        if( !$res ) {
            $this->error( 'SYSTEM_ERR' );
        }
        return $this;
    }

    private function save() {
        $image = $this->params['image'];

        if( is_uploaded_file( $image['tmp_name'] ) ) {
            $md5 = md5_file( $image['tmp_name'] );

            $filename = $md5 . $this->mime2Ext[ $image['type'] ];

            $response = \Local\FileManage::saveAvatar( $filename, $image['tmp_name'] );

            if( $response->status != 200 ) {
                $this->error( 'SYSTEM_ERR' );
            }

            $conf = \Local\Utils::loadConf( 'attachment', 'avatar' );

            $this->data['img'] = $filename;

            $this->data['url'] = $conf->cdn . '/' . $filename;
        } else {
            $this->error( 'SYSTEM_ERR' );
        }

        return $this;
    }

    private function check() {
        $image = $this->params['image'];

        $conf = \Local\Utils::loadConf( 'attachment', 'avatar' );

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
        $image = $this->request->getFiles('image');

        if( is_null( $image ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        if( $image['error'] > 0 ) {
            switch( $image['error'] ) {
                case 1 :
                case 2 :
                    $this->error( 'EXCEEDED_MAX' );
                    break;
                default :
                    $this->error( 'PARAMS_ERR' );
                    break;
            }
        }

        $this->params = [
            'image' => $image
        ];

        return $this;

    }
}
