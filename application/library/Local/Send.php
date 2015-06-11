<?php

namespace Local;

Class Send {
    static public function shortMessage() {
    }

    static public function email( $params, $addresses ) {
        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->Host = 'smtp.163.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'kelcb@163.com';
        $mail->Password = 'Mr.LvChengbin324';
        $mail->SMTPSecure = 'tls';

        $mail->From = 'kelcb@163.com';
        $mail->FromName = 'GROUPLE.COM';


        foreach( $addresses as $address ) {
            $mail->addAddress( $address[ 0 ], $address[ 1 ] );
        }

        foreach( $params as $key => $value ) {
            $mail->$key = $value;
        }

        if( !$mail->send() ) {
            echo $mail->ErrorInfo;
            return false;
        }

        return true;
    }
}
