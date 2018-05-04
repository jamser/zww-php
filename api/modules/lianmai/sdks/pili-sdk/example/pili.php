<?php
require(join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), 'lib', 'Pili_v2.php')));
$ak = "Ge_kRfuV_4JW0hOCOnRq5_kD1sX53bKVht8FNdd3";
$sk = "0fU92CSrvgNJTVCXqbuRVqkntPFJLFERGa4akpko";
$hubName = "PiliSDKTest";
$mac = new Qiniu\Pili\Mac($ak, $sk);
$client = new Qiniu\Pili\Client($mac);
try{
    $hub = $client->hub($hubName);
    $streamKey = "php-sdk-test" . time();
    $stream = $hub->stream($streamKey);
    print_r($stream);
}catch (\Exception $e){
    echo "Error:", $e, "\n";
}