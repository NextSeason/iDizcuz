<?php

Class GetActivitiesAction extends \Local\BaseAction {
    private $data = [];
    protected $type = 'interface';

    public function __execute() {
        $this->paramsProcessing()->getActivities()->getExtra();
        return $this->data;
    }

    public function __mobile() {
        return $this->__execute();
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

        if( $post['to'] != 0 ) {
            $postModel = new PostModel();
            $post['to'] = $postModel->get( $post['to'], [ 'id', 'title' ] );
        }

        $post['mark'] = 0; 
        $post['own'] = 0;

        if( $this->account ) {
            if( $this->account['id'] == $post['account_id'] ) {
                $post['own'] = 1;
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

    private function getComment( $id ) {
        $commentModel = new CommentModel();
        $comment = $commentModel->get( $id );

        $postDataModel = new PostDataModel();
        $post_data = $postDataModel->get( $comment['post_id'] );

        if( !$post_data || $post_data['status'] > 0 ) {
            $comment['post'] = null;
        } else {
            $postModel = new PostModel();
            $post = $postModel->get( $comment['post_id'] );
            $post['data'] = $post_data;
            $comment['post'] = $post;
        }

        return $comment;
    }

    private function getAccount( $id ) {
        $accountModel = new AccountModel();
        $account = $accountModel->get( $id, ['id','uname', 'desc', 'img', 'industry', 'employment', 'position' ] );

        if( !$account ) {
            return null;
        }

        $industry = $account['industry'];

        /**
         * if the value of industry is not 0, get industry info from configuration file
         */
        if( $industry != 0 ) {
            $industries = \Local\Utils::loadConf( 'industries', 'list' );

            if( !isset( $industries[ $industry ] ) ) {
                // if the industry cannot be found in industry list, set the value to 0
                $account['industry'] = 0;
            } else {
                $account[ 'industry' ] = $industries[ $industry ];
            }
        }

        $accountDataModel = new AccountDataModel();
        $account['data'] = $accountDataModel->get( $id );

        return $account;
    }

    private function getExtra() {
        if( !count( $this->data['activities'] ) ) {
            return $this;
        }

        foreach( $this->data['activities'] as &$activity ) {

            /**
             * 0 post 
             * 1 agree 
             * 2 disagree 
             * 3 comment
             * 4 follow 
             * 5 signup
             */
            switch( $activity['type'] ) {
                case 0 :
                case 1 :
                case 2 :
                    $activity['extra'] = $this->getPost( $activity['relation_id'] );
                    break;
                case 3 :
                    $activity['extra'] = $this->getComment( $activity['relation_id'] );
                    break;
                case 4 :
                case 5 :
                    if( $this->account && $activity['relation_id'] == $this->account['id'] ) {
                        $account = $this->getAccount( $activity['account_id'] );
                        $account['isself'] = 1;
                    } else {
                        $account = $this->getAccount( $activity['relation_id'] );
                    }

                    $account[ 'followed' ] = 0;

                    if( $this->account ) {
                        $followModel = new FollowModel();

                        $follow = $followModel->getFollowStatus( [
                            'account_id' => $account['id'],
                            'fans_id' => $this->account['id']
                        ] );

                        if( $follow ) {
                            $account['followed'] = 1;
                        }
                    }

                    $activity['extra'] = $account;
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

        $accountModel = new AccountModel();

        foreach( $activities as &$activity ) {
            $activity['account'] = $accountModel->get( $activity['account_id'], ['id','uname'] );
        }
        $this->data['activities'] = $activities;

        return $this;
    }

    private function paramsProcessing() {
        $cursor = intval( $this->__getQuery( 'cursor' ) );

        if( $cursor < 0 ) $cursor = 0;

        $account_id = $this->__getQuery( 'account_id' );

        $follower_id = $this->__getQuery( 'follower_id' );

        $rn = intval( $this->__getQuery( 'rn' ) );

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
