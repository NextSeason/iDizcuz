<?php

Class IndexAction extends \Yaf\Action_Abstract {
    public function execute() {
        $accountModel = new AccountModel();

        echo $accountModel->get();
        echo $this->abc();
    }

    private function abc() {
        return ':xxxxx::';
    }
}
