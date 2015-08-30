<?php

Class SaveSettingsAction extends \Local\BaseAction {
    private $data = [];
    protected $type = 'interface';

    public function __execute() {
        if( !$this->account ) {
            $this->error( 'NOTLOGIN_ERR' );
        }

        $this->paramsProcessing()->save();


        return $this->data;
    }

    public function save() {
        $accountSettingsModel = new AccountSettingsModel();

        $accountSettingsModel->update( $this->account['id'], [
            $this->params[ 'key' ] => $this->params[ 'value' ]
        ] );

        return $this;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function paramsProcessing() {
        $key = $this->__getPost( 'k' );
        $value = $this->__getPost( 'v' );

        if( is_null( $key ) || is_null( $value ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $this->params = [
            'key' => $key,
            'value' => $value
        ];

        return $this;
    }
}
