<?php

Class ArticleAction extends \Local\MisAction {
    private $data = [];

    public function __execute() {

        $this->tpl = 'mis/article';

        return $this->data;
    }
}
