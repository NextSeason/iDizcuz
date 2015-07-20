<?php

Class GetActivitiesAction extends \Local\BaseAction {
    private $data = [];
    protected $type = 'interface';

    public function __execute() {
        $this->paramsProcessing()->getActivities()->getExtra();
        return $this->data;
    }

    private function getPost( $id ) {
        $postDataModel = new PostDataModel();

        $post_data = $postDataModel->get( $id );

        if( !$post_data || $post_data['status'] > 0 ) {
            return null;
        }

        $combinationModel = new CombinationModel();

        $post = $combinationModel->getPost( [ 'id' => $id ] );

        if( !$post ) return null;

        $post['mark'] = 0; 
        $post['mine'] = 0;

        if( $this->account ) {
            if( $this->account['id'] == $post['account_id'] ) {
                $post['mine'] = 1;
            } else {
                $markModel = new MarkModel();
                $mark = $markModel->getMarkByPostAndAccount( 
                    $post['id'], 
                    $this->account['id'] 
                );

                if( $mark ) $post['mark'] = $mark['id'];
            }
        }

        $post['data'] = $post_data;

        return $post;

    }

    private function getExtra() {
        if( !count( $this->data['activities'] ) ) {
            return $this;
        }

        foreach( $this->data['activities'] as &$activity ) {

            switch( $activity['type'] ) {
                case 0 :
                case 1 :
                case 2 :
                case 3 :
                    $activity['extra'] = $this->getPost( $activity['relation_id'] );
                    break;
                case 4 :
                    break;
                default :
                    $activity['extra'] = null;
                    continue;

            }
        }
        return $this;
    }

    private function getActivities() {
        $params = $this->params;

        $activityModel = new ActivityModel();

        if( !is_null( $params['account_id'] ) && $params['account_id'] != 0 ) {
            $activities = $activityModel->getActivitiesByAccount( [
                'cursor' => $params['cursor'],
                'rn' => $params['rn'],
                'account_id' => $params['account_id']
            ] );
        } else if( !is_null( $params['follower_id'] ) && $params['follower_id'] != 0 ) {
            $activities = $activityModel->getFollowingActivities( [
                'cursor' => $params['cursor'],
                'rn' => $params['rn'],
                'follower_id' => $params['follower_id']
            ] );
        } else {
            $activities = $activityModel->getActivities( [
                'cursor' => $params['cursor'],
                'rn' => $params['rn']
            ] );
        }

        if( $activities === false ) {
            $this->error( 'SYSTEM_ERR' );
        }
        $this->data['activities'] = $activities;

        return $this;
    }

    private function paramsProcessing() {
        $request = $this->request;

        $cursor = intval( $request->getQuery( 'cursor' ) );

        if( $cursor < 0 ) $cursor = 0;

        $account_id = $request->getQuery( 'account_id' );

        $follower_id = $request->getQuery( 'follower_id' );

        $rn = intval( $request->getQuery( 'rn' ) );

        if( $rn < 1 ) $rn = 20;

        $this->params = [
            'cursor' => $cursor,
            'rn' => $rn,
            'account_id' => $account_id,
            'follower_id' => $follower_id
        ];

        return $this;
    }
}
