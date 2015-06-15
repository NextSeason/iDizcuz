<?php

Class SignoutAction extends \Local\BaseAction {

    public function __execute() {
        $this->type = 'interface';

        $this->session[ 'account' ] = null;
    }
}
