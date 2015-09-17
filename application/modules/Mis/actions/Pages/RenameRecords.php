<?php

Class RenameRecordsAction extends \Local\MisAction {
    private $data = [];
    protected $tpl = 'mis/renameRecords';

    public function __execute() {
        $this->paramsProcessing()->getRecords();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function getRecords() {
        $accountRenameModel = new AccountRenameModel();

        $where = [];

        if( !is_null( $this->params['status'] ) ) {
            $where[] = [ 'status', $this->params['status'] ];
        }

        $renameRecords = $accountRenameModel->select( [
            'where' => $where,
            'rn' => 40
        ] );

        $this->data['renameRecords'] = $renameRecords;
        return $this;
    }

    private function paramsProcessing() {
        $status = $this->__getQuery( 'status' );

        $this->params = [
            'status' => $status
        ];

        $this->data['status'] = $status;
        return $this;
    }
}
