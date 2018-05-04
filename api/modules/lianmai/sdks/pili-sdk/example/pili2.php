<?php

require(join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), 'lib', 'Pili_v2.php')));

$ak="QzdCUKE0lXmIJsvJ_yQJTeIsJYeK6liEdWAn9JuU";
$sk="HwIhvaYrUZ4pedSRTpKZsqunSty0uUpQdrFDGLGU";

$mac = new Qiniu\Pili\Mac($ak, $sk);
$client = new Qiniu\Pili\RoomClient($mac);

try{
    $resp=$client->kickingPlayer("testroom","qiniu-f6e07b78-4dc8-45fb-a701-a9e158abb8e6");
    print_r($resp);
}catch (\Exception $e){
    echo "Error:", $e, "\n";
}