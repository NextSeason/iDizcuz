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
}
