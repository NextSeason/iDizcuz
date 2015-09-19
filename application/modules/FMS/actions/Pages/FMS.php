<?php

Class FMSAction extends \Local\MisAction {
    private $data = [];

    public function __execute() {
        $this->paramsProgressing()->getFragment();

        $this->tpl = 'fms/' . $this->params['page'];

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function getFragment() {
        $fragment = \FMS\Api::getFragmentByAlias( $this->params['page'] );

        if( !$fragment ) return $this;
        $fragment['content'] = json_decode( $fragment['content'], true );
        $this->data['fragment'] = $fragment;
        return $this;
    }

    private function paramsProgressing() {
        $page = $this->__getParam( 'page' );

        $this->params = [
            'page' => $page
        ];
        return $this;
    }
}
