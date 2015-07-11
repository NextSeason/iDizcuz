<?php

namespace Local;

Class Error {

    public static $SYSTEM_ERR = array(
        'errno' => 1,
        'errmsg' => 'system error'
    );
    public static $PARAMS_ERR = array(
        'errno' => 2,
        'errmsg' => 'unvalid paramaters for the request'
    );

    public static $NOTLOGIN_ERR = array(
        'errno' => 3,
        'errmsg' => 'user not login'
    );

    public static $VCODE_ERR = array(
        'errno' => 4,
        'errmsg' => 'vcode is not match'
    );

    public static $ACCOUNT_EXISTS = array(
        'errno' => 5,
        'errmsg' => 'account has already exists'
    );

    public static $ACCOUNT_NOTEXISTS = array(
        'errno' => 6,
        'errmsg' => 'account is not exists'
    );

    public static $PASSWD_ERR = array(
        'errno' => 7,
        'errmsg' => 'password is not correct'
    );

    public static $USER_BANNED = array(
        'errno' => 8,
        'errmsg' => 'user has been banned'
    );

    public static $TOPIC_NOTEXISTS = array(
        'errno' => 9,
        'errmsg' => 'topic is not exists'
    );

    public static $POINT_NOTEXISTS = array(
        'errno' => 10,
        'errmsg' => 'point is not exists'
    );

    public static $POST_NOTEXISTS = array(
        'errno' => 11,
        'errmsg' => 'post is not exists' 
    );

    public static $REACHED_MAX = array(
        'errno' => 12,
        'errmsg' => 'reached the max value'
    );

    public static $REACHED_MIN = array(
        'errno' => 13,
        'errmsg' => 'reached the min value' 
    );

    public static $REPORTED_POST = array(
        'errno' => 14,
        'errmsg' => 'already report this post'
    );

    public static $CONTENT_TOOSHORT = array(
        'errno' => 15,
        'errmsg' => 'content is too short'
    );

    public static $CONTENT_TOOLONG = array(
        'errno' => 16,
        'errmsg' => 'content is too long'
    );

    public static $EXCEEDED_MAX = array(
        'errno' => 17,
        'errmsg' => 'value beyond the maximum'
    );

    public static $UNSUPPORT_MIME = array(
        'errno' => 18,
        'errmsg' => 'unsupported file type'
    );

    public static $FAILED_SAVE = array(
        'errno' => 19,
        'errmsg' => 'failed to save file'
    );
}
