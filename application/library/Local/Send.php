<?php

namespace Local;

Class Send {
    static public function shortMessage() {
    }

    static public function email( $params, $addresses ) {
        $mail = new PHPMailer();

        $emailConf = \Local\Utils::loadConf( 'email', 'system' );
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = $emailConf->smtp->host;
        $mail->SMTPAuth = true;
        $mail->Username = $emailConf->address;
        $mail->Password = $emailConf->smtp->password;
        $mail->SMTPSecure = 'tls';

        $mail->From = $emailConf->address;
        $mail->FromName = '每日论点•iDizcuz.com';

        $mail->IsHTML( true );

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
