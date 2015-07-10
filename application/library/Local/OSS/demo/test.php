<?php

require_once '../sdk.class.php';

$oss_sdk_service = new ALIOSS( '5kF8Ke690quHol1B', 'tAaCeWNSxYFlVQ5TMaamHBJP6devvF' );

$oss_sdk_service->set_debug_mode( TRUE );

$bucket = 'idizcuz-user-avatars';

$option = array(
);

$response = $oss_sdk_service->list_object( $bucket, array() );

_format( $response );


function _format($response) {
	echo '|-----------------------Start---------------------------------------------------------------------------------------------------'."\n";
	echo '|-Status:' . $response->status . "\n";
	echo '|-Body:' ."\n"; 
	echo $response->body . "\n";
	echo "|-Header:\n";
	print_r ( $response->header );
	echo '-----------------------End-----------------------------------------------------------------------------------------------------'."\n\n";
}
