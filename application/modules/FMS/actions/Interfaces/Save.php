<?php

Class SaveAction extends \Local\MisAction {
    private $data = [];
    protected $type = 'interface';

    public function __execute() {
        $this->paramsProgressing()->save();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function save() {
        $fmsModel = new FMSModel();

        $fms = $fmsModel->select( [
            'columns' => [ 'id' ],
            'where' => [
                [ 'alias', $this->params['alias'] ]
            ]
        ] );

        return $fms ? $this->update( $fms[0] ) : $this->insert();
    }

    private function update( $fms ) {
        /*
        $fmsHistoryModel = new FMSHistoryModel();

        $history = $fmsHistoryModel->insert( [
            'alias' => $fms['alias'],
            'content' => $fms['content']
        ] );

        if( !$history ) {
            $this->error( 'SYSTEM_ERR' );
        }
         */

        $fmsModel = new FMSModel();
        $res = $fmsModel->update( [
            'set' => [
                'content' => $this->params[ 'content' ]
            ],
            'where' => [
                [ 'alias', $this->params['alias'] ]
            ]
        ] );

        if( !$res ) $this->error( 'SYSTEM_ERR' );

        return $this;
    }

    private function insert() {
        $fmsModel = new FMSModel();

        $res = $fmsModel->insert( [
            'alias' => $this->params['alias'],
            'content' => $this->params['content']
        ] );

        if( !$res ) $this->error( 'SYSTEM_ERR' );

        return $this;
    }

    private function paramsProgressing() {
        $alias = $this->__getPost( 'alias' );
        if( is_null( $alias ) || !$alias ) {
            $this->error( 'PARAM_ERR' );
        }
        $content = $this->__getPost( 'content' );

        if( !count( $content) ) {
            $this->error( 'PARAM_ERR' );
        }

        $this->params = [
            'alias' => $alias,
            'content' => $content
        ];
        return $this;
    }
}
