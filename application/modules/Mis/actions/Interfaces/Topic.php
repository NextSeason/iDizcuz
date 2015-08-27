<?php

Class TopicAction extends \Local\MisAction {

    private $data = array();
    private $transactionModel;

    public function __execute() {

        $this->type = 'interface';

        $this->paramsProcessing();

        if( is_null( $this->params['id'] ) || $this->params['id'] == 0 ) {
            $this->addTopic();
        } else {
            $this->updateTopic();
        }

        return $this->data;
    }

    private function updateTopic() {
        $params = $this->params;

        $transactionModel = new TransactionModel();
        $res = $transactionModel->updateTopic( [
            'id' => $params['id'],
            'cid' => $params['cid'],
            'type' => $params['type'],
            'title' => $params['title'],
            'desc' => $params['desc'],
            'points' => $params['points']
        ] );

        if( !$res ) {
            $this->error( 'SYSTEM_ERR' );
        }
        return $this;
    }
    
    private function addTopic() {
        $params = $this->params;

        $data = array(
            'cid' => $params[ 'cid' ],
            'type' => $params[ 'type' ],
            'title' => $params[ 'title' ],
            'desc' => $params[ 'desc' ],
            'points' => $params['points']
        );
        $transactionModel = new TransactionModel();

        $topic_id = $transactionModel->addTopic( $data );

        if( !$topic_id ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data['topic_id'] = $topic_id;
        return $this;
    }

    private function paramsProcessing() {
        $id = $this->__getPost( 'id' );

        $title = $this->__getPost( 'title' );

        if( is_null( $title ) ) {
            $this->error( 'PARAMS_ERR', 'Topic title is null' );
        }

        $len = mb_strlen( $title );

        if( !$len ) {
            $this->error( 'PARAMS_ERR', 'Topic title is null' );
        }

        if( $len > 120 ) {
            $this->error( 'PARAMS_ERR', 'Topic title is too long' );
        }

        $desc = $this->__getPost( 'desc' );

        if( is_null( $desc ) || !strlen( $desc) ) {
            $this->error( 'PARAMS_ERR', 'Topic description is empty' );
        }

        $type = $this->__getPost( 'type' );

        if( !isset( $type ) || !in_array( $type, array( 2, 1 ) ) ) {
            $this->error( 'PARAMS_ERR', 'You need to select a type for this topic' );
        }

        $cid = $this->__getPost( 'cid' );

        if( empty( $cid ) ) {
            $this->error( 'PARAMS_ERR', 'You must to select a category for this topic' ); 
        }

        $points = $this->__getPost( 'points' );

        if( $type == 1 && ( is_null( $points ) || !strlen( $points ) ) ) {
            $this->error( 'PARAMS_ERR', 'points is required' );
        }

        $this->params = array(
            'id' => $id,
            'title' => $title,
            'desc' => $desc,
            'type' => $type,
            'cid' => $cid,
            'points' => $points
        );
        return $this;
    }
}
