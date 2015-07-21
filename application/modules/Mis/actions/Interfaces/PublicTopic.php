<?php

Class PublicTopicAction extends \Local\MisAction {
    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing()->update();

        return $this->data;
    }

    private function update() {
        $topicDataModel = new TopicDataModel();

        $res = $topicDataModel->update( $this->params['id'], [
            'status' => $this->params['status']
        ] );

        if( !$res ) {
            $this->error( 'SYSTEM_ERR' );
        }

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $id = $request->getPost('id');
        $status = $request->getPost( 'status' );

        $this->params = [
            'id' => $id,
            'status' => $status
        ];

        return $this;
    }
}
