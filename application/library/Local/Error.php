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
}
