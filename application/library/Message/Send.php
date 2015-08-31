<?php

namespace Message;

Class Send {

    static private $accountSettingsModel = null;

    static private $transactionModel = null;

    static private $allowMessageType = [
        'view_to', 'comment', 'reply_comment', 'agree', 'disagree'
    ];

    static private function getModel() {
        return self::accountSettingsModel ? self::accountSettingsModel : new \AccountSettingsModel();
    }

    static public function getAccountSettings( $account_id, $key ) {
        $accountSettingsModel = new AccountSettingsModel();

        $settings = $accountSettingsModel->get( $account_id );

        if( !$settings ) {
            return false;
        }

        return isset( $settings[ 'msg_' . $key ] ) ? $settings[ 'msg_' . $key ] : false;
    }

    static private function send( $type, $from, $to, $params ) {
        $transactionModel = self::transactionModel ? self::transactionModel : new TransactionModel();

        $accountId = $this->pool['to_post_data']['account_id'];
 
         $accountSettingsModel = new AccountSettingsModel();
 
         $accountSettings = $accountSettingsModel->get( $accountId );
 
         $view = $this->getView();
 
         $conf = \Local\Utils::loadConf( 'message', 'post' );
 
         $data = [ 
             'from' => 0,
             'to' => $to,
             'type' => $conf->type,
             'title' => $view->render( $conf->template, array(
                 'account' => $this->account,
                 'post' => [
                     'id' => \Local\Utils::encodeId( $this->data['id'] ),
                     'title' => $this->params[ 'title' ]
                 ]   
             ) ),
             'content' => ''
         ];  
 
         $this->transactionModel->sendMessage( $data );
 

    }

    static public function viewToMsg( $to, $params ) {
        self::send( $type = '
                
        );
    }

    static public function commentMsg() {
    }

    static public function replyCommentMsg() {
    }

    static public function agreeMsg() {
    }

    static public function disagreeMsg() {
    }

}
