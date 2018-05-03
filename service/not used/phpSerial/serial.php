<?php

include "php_serial.php";

$serial = new phpSerial;
$serial->deviceSet("/dev/ttyUSB0");
$serial->confBaudRate(921600);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(1);
$serial->confFlowControl("none");

$serial->deviceOpen();

/**
 * Worked converting method.
 * @param hex $number
 * @return float
 */
function hex2float($number) {
    $binfinal = sprintf("%032b",hexdec($number));
    $sign = substr($binfinal, 0, 1);
    $exp = substr($binfinal, 1, 8);
    $mantissa = "1".substr($binfinal, 9);
    $mantissa = str_split($mantissa);
    $exp = bindec($exp)-127;
    $significand=0;
    for ($i = 0; $i < 24; $i++) {
        $significand += (1 / pow(2,$i))*$mantissa[$i];
    }
    return $significand * pow(2,$exp) * ($sign*-2+1);
}

//  Loop reading data.
while( 1 == 1){
    $data = $serial->readPort();
    if(($data != "") && (strlen($data) >= 48)){
        $shift = substr($data,strpos($data, "ff01"),48);
        if(strlen($shift) == 48){
            $yaw = substr($shift, 38, 2) . substr($shift, 36, 2) . substr($shift, 34, 2) . substr($shift, 32, 2);
            $angle = round(hex2float($yaw),2);
            if( ($angle >= -180) && ($angle <= 180) && ($angle != 0) && ($angle != -0) ){
                echo($angle . "\n");
            }
        }
    }  
   usleep(50000);
}

$serial->deviceClose();

?>