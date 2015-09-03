<?php

namespace Message;

Class Send {

    static private $accountSettingsModel;
    static private $transactionModel;
    static private $view;

    static private function getAccountSettingsModel() {
        if( self::$accountSettingsModel ) return self::$accountSettingsModel;
        self::$accountSettingsModel = new \AccountSettingsModel();
        return self::$accountSettingsModel;
    }

    static private function getTransactionModel() {
        if( self::$transactionModel )return self::$transactionModel;
        self::$transactionModel = new \TransactionModel();
        return self::$transactionModel;
    }

    static private function getView() {
        if( self::$view ) return self::$view;
        self::$view = new \Yaf\View\Simple( APP_PATH . '/application/views/page/message' );
        return self::$view;
    }

    static public function getAccountSettings( $account_id, $key ) {
        $accountSettingsModel = new \AccountSettingsModel();

        $settings = $accountSettingsModel->get( $account_id );

        if( !$settings ) {
            return false;
        }

        return isset( $settings[ 'msg_' . $key ] ) ? $settings[ 'msg_' . $key ] : false;
    }

    static private function send( $type, $from, $to, $params ) {
        $transactionModel = self::getTransactionModel(); 

        $data = [ 
            'from' => $from,
            'to' => $to,
            'type' => $type,
            'title' => $params['title'],
            'content' => isset( $params['content'] ) ? trim( $params['content'] ) : ''
        ];  
 
        self::getTransactionModel()->sendMessage( $data );
    }

    static public function viewToMessage( $from, $to, $params ) {
        $setting = intval( self::getAccountSettings( $to, 'view_to' ) );

        if( $setting == 3 ) return;

        if( $setting == 2 && !\User\Follow::check( $from, $to ) ) return;

        // message type is 1
        self::send( 1, $from, $to, [
            'title' => self::getView()->render( 'post.msg.html', \Local\Utils::traverseEncodeId( $params ) )
        ] );
    }

    static public function commentMessage( $from, $to, $params ) {
        $setting = intval( self::getAccountSettings( $to, 'comment' ) );

        if( $setting == 3 ) return;
        if( $setting == 2 && !\User\Follow::check( $from, $to ) ) return;

        // message type is 2
        self::send( 2, $from, $to, [
            'title' => self::getView()->render( 'comment.msg.html', \Local\Utils::traverseEncodeId( $params ) ),
            'content' => $params[ 'content' ]
        ] );
    }

    static public function replyCommentMessage( $from, $to, $params ) {
        $setting = intval( self::getAccountSettings( $to, 'reply_comment' ) );

        if( $setting == 3 ) return;
        if( $setting == 2 && !\User\Follow::check( $from, $to )  ) return;

        // message type is 3
        self::send( 3, $from, $to, [
            'title' => self::getView()->render( 'reply.msg.html', \Local\Utils::traverseEncodeId( $params ) ),
            'content' => $params['content']
        ] );
    }

    static public function agreeMessage( $from, $to, $params ) {
        $setting = intval( self::getAccountSettings( $to, 'agree' ) );

        if( $setting == 3 ) return;
        if( $setting == 2 && !\User\Follow::check( $from, $to ) ) return;

        // message type is 4
        self::send( 4, $from, $to, [
            'title' => self::getView()->render( 'agree.msg.html', \Local\Utils::traverseEncodeId( $params ) )
        ] );
    }

    static public function disagreeMessage( $from, $to, $params ) {
        $setting = intval( self::getAccountSettings( $to, 'disagree' ) );

        if( $setting == 3 ) return;
        if( $setting == 2 && !\User\Follow::check( $from, $to ) ) return;

        // message type is 5
        self::send( 5, $from, $to, [
            'title' => self::getView()->render( 'disagree.msg.html', \Local\Utils::traverseEncodeId( $params ) )
        ] );
    }

    static public function newFansMessage( $from, $to, $params = [] ) {
        $setting = intval( self::getAccountSettings( $to, 'new_fans' ) );
        if( $setting == 2 ) return;

        // message type is 6
        self::send( 6, $from, $to, [
            'title' => self::getView()->render( 'newfans.msg.html', \Local\Utils::traverseEncodeId( $params ) )
        ] );
    }
}
