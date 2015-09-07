<?php 
namespace Posts;

Class Api {
    static private $postModel;
    static private $postDataModel;

    static private function getPostModel() {
        if( self::$postModel ) return self::$postModel;
        self::$postModel = new \PostModel();
        return self::$postModel;
    }

    static private function getPostDataModel() {
        if( self::$postDataModel ) return self::$postDataModel;
        self::$postDataModel = new \PostDataModel();
        return self::$postDataModel;
    }

    static public function get( $id, $columns = null ) {
        return self::getPostModel()->get( $id, $columns );
    }

    static public function getData( $id, $columns = null ) {
        return self::getPostDataModel()->get( $id, $columns );
    }
}
