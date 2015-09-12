<?php

Class EditorUploadImagesAction extends \Local\MisAction {

    private $data = [];
    protected $type = 'interface';

    /**
     * response code for ueditor
     */
    private $stateMap = array(    //上传状态映射表，国际化用户需考虑此处数据的国际化
        0 => "SUCCESS" ,                //上传成功标记，在UEditor中内不可改变
        1 => "文件大小超出 upload_max_filesize 限制" ,
        2 => "文件大小超出 MAX_FILE_SIZE 限制" ,
        3 => "文件未被完整上传" ,
        4 => "没有文件被上传" ,
        5 => "上传文件为空" ,
        "POST" => "文件大小超出 post_max_size 限制" ,
        "SIZE" => "文件大小超出网站限制" ,
        "TYPE" => "不允许的文件类型" ,
        "DIR" => "目录创建失败" ,
        "IO" => "输入输出错误" ,
        "UNKNOWN" => "未知错误" ,
        "MOVE" => "文件保存时出错",
        "DIR_ERROR" => "创建目录失败"
    );

    private $mime2Ext = array(
        'image/jpeg' => '.jpg',
        'image/gif' => '.gif',
        'image/bmp' => '.bmp',
        'image/png' => '.png'
    );

    public function __execute() {
        $this->type = 'interface';

        /**
         * ueditor is so stupid that it can only receive html 
         * because it use form submit to upload images
         */
        header( 'Content-Type:text/html; charset=utf-8' );

        if( !$this->admin ) {
            $this->_error( \Local\Error::$NOTLOGIN_ERR );
        }

        $this->paramsProcessing()->check()->save();

        $this->_success();
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function save() {
        $image = $this->params[ 'image' ];

        if( is_uploaded_file( $image[ 'tmp_name' ] ) ) {

            $md5 = md5_file( $image['tmp_name'] );

            $filename = $md5 . $this->mime2Ext[ $image['type'] ];


            $response = \Local\FileManage::saveTopicstc( $filename, $image[ 'tmp_name' ] );

            if( $response->status != 200 ) {
                $this->_error( \Local\Error::$SYSTEM_ERR, 'UNKNOW' );
            }

            $conf = \Local\Utils::loadConf( 'attachment', 'topics_and_events' );

            $this->data[ 'url' ] = $conf->cdn . '/' . $filename;

        } else {
            $this->_error( \Local\Error::$SYSTEM_ERR, 'UNKNOW' );
        }
    }

    private function check() {
        $image = $this->params[ 'image' ];
        $conf = \Local\Utils::loadConf( 'attachment', 'topics_and_events' );

        if( $image['size'] > $conf->maxsize ) {
            $this->_error( \Local\Error::$EXCEEDED_MAX, 'SIZE' );
        }

        $mimes = explode( '|', $conf->allowmime );

        if( !in_array( $image['type'], $mimes ) ) {
            $this->_error( \Local\Error::$UNSUPPORT_MIME, 'TYPE' );
        }
        return $this;
    }

    private function _success() {
        $image = $this->params[ 'image' ];

        $url = $this->data[ 'url' ];

        $type = explode( '.', $url );

        $this->data[ 'url' ] = $url;
        $this->data[ 'originalName' ] = $image['name'];
        $this->data[ 'name' ] = $url;
        $this->data[ 'type' ] = '.' . end( $type );
        $this->data[ 'size' ] = $image[ 'size' ];
        $this->data[ 'state' ] = $this->stateMap[ 0 ];

        echo json_encode( $this->data );
        exit;
    }

    private function _error( $err, $state = 'UNKNOWN' ) {
        $this->data = array_merge( $this->data, $err );
        $this->data[ 'state' ] = $this->stateMap[ $state ];

        echo json_encode( $this->data );
        exit;
    }

    private function paramsProcessing() {
        $image = $this->request->getFiles( 'image' ); 

        if( is_null( $image ) ) {
            $this->_error( \Local\Error::$PARAMS_ERR );
        }

        if( $image['error'] > 0 ) {
            switch( $image['error'] ) {
                case 1 :
                case 2 :
                    $this->_error( \Local\Error::$EXCEEDED_MAX, 'SIZE' );
                    break;
                default :
                    $this->_error( \Local\Error::$PARAMS_ERR );
                    break;
            }
        }

        $this->params[ 'image' ] = $image;

        return $this;
    }

}
