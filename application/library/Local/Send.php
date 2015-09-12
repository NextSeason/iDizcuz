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
        $mail->Port = $emailConf->smtp->port;

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

    static public function ___email( $params ) {
        $emailConf = \Local\Utils::loadConf( 'email', 'system' );

        $params = array_merge( [
            'api_user' => $emailConf->sendcloud->api_user,
            'api_key' => $emailConf->sendcloud->api_key,
            'from' => $emailConf->address,
            'fromname' => '每日论点•iDizcuz',
            'resp_email_id' => 'true'
        ], $params );

        $data = http_build_query($params);

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $data
        ));

        $context  = stream_context_create($options);
        $result = file_get_contents($emailConf->sendcloud->url, false, $context);

        print_r( $params );

        return $result;
    }
}
