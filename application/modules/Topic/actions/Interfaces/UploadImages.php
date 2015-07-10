<?php

Class UploadImagesAction extends \Local\BaseAction {
    private $data = [];

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

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing();

        return $this->data;
    }

    private function paramsProcessing() {

        $

        return $this;
    }
}
