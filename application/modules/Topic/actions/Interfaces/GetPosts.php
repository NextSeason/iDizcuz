<?php

Class GetPostsAction extends \Local\BaseAction {

    private $data = [];

    public function __execute() {
        $this->type = 'interface';

        $this->paramsProcessing();

        $this->data[ 'posts' ] = $this->getPosts();
            
        $this->getTotal();

        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
    }

    private function getTotal() {
        $params = $this->params;

        if( $params['point_id'] != 0 ) {
            $pointDataModel = new PointDataModel();

            $point_data = $pointDataModel->get( $params['point_id'] );

            if( !$point_data ) {
                $this->error( 'SYSTEM_ERR' );
            }

            $this->data['total'] = $point_data['post_cnt'];
            return $this;
        }

        $topicDataModel = new TopicDataModel();

        $topic_data = $topicDataModel->get( $params['topic_id'] );

        if( !$topic_data ) {
            $this->error( 'SYSTEM_ERR' );
        }

        $this->data['total'] = $topic_data['post_cnt'];

        return $this;
    }

    private function getPosts() {
        $params = $this->params;

        $postDataModel = new PostDataModel();

        $where = [
            [ 'status', 0 ],
            [ 'topic_id', $params[ 'topic_id' ] ]
        ];

        if( $params['point_id'] != 0 ) {
            $where[] = [ 'point_id', $params[ 'point_id' ] ];
        }

        $postsData = $postDataModel->select( [
            'columns' => [ 'id' ],
            'where' => $where,
            'order' => [ [ [ 'id', 'DESC' ], [ 'agree', 'DESC' ], [ 'disagree', 'DESC' ] ][ $params['order'] ] ],
            'start' => $params['start'],
            'rn' => $params['rn']
               
        ] );

        if( $postsData === false ) {
            $this->error( 'SYSTEM_ERR' );
        }

        foreach( $postsData as &$postData ) {
            $postData = $postData['id'];
        }

        return $postsData;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $topic_id = $request->getQuery( 'topic' );

        if( is_null( $topic_id ) ) {
            $this->error( 'PARAMS_ERR' );
        }

        $point_id = intval( $request->getQuery( 'point' ) );

        $order = intval( $request->getQuery( 'order' ) );

        if( $order > 2 ) $order = 0;

        $start = intval( $request->getQuery( 'start' ) );

        if( $start < 0 ) $start = 0;

        $rn = intval( $request->getQuery( 'rn' ) );

        if( $rn == 0 ) $rn = 20;
        if( $rn > 100 ) $rn = 100;

        $this->params = [
            'topic_id' => $topic_id,
            'point_id' => $point_id,
            'order' => $order,
            'start' => $start,
            'rn' => $rn
        ];

        return $this;
    }
}
