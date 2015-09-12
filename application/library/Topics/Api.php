<?php

namespace Topics;

Class Api {
    static private $topicModel;
    static private $topicDataModel;

    static private function getTopicModel() {
        if( self::$topicModel ) return self::$topicModel;
        self::$topicModel = new \TopicModel();
        return self::$topicModel;
    }

    static private function getTopicDataModel() {
        if( self::$topicDataModel ) return self::$topicDataModel;
        self::$topicDataModel = new \TopicDataModel();
        return self::$topicDataModel;
    }

    static public function get( $id, $columns = null ) {
        return self::getTopicModel()->get( $id, $columns );
    }

    static public function getData( $id, $columns = null ) {
        return self::getTopicDataModel()->get( $id, $columns );
    }

}
