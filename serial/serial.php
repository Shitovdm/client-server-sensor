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

function binToFloat($bin) { 
    if(strlen($bin) > 32) { 
        return false; 
    } else if(strlen($bin) < 32) { 
        $bin = str_repeat('0', (32 - strlen($bin))) . $bin; 
    } 

    $sign = 1; 
    if(intval($bin[0]) == 1) { 
        $sign = -1; 
    } 

    $binExponent = substr($bin, 1, 8); 
    $exponent = -127; 
    for($i = 0; $i < 8; $i++) { 
        $exponent += (intval($binExponent[7 - $i]) * pow(2, $i)); 
    } 

    $binBase = substr($bin, 9);            
    $base = 1.0; 
    for($x = 0; $x < 23; $x++) { 
        $base += (intval($binBase[$x]) * pow(0.5, ($x + 1))); 
    } 

    $float = (float) $sign * pow(2, $exponent) * $base; 

    return $float; 
}

function hexTo32Float($strHex) {
    $v = hexdec($strHex);
    $x = ($v & ((1 << 23) - 1)) + (1 << 23) * ($v >> 31 | 1);
    $exp = ($v >> 23 & 0xFF) - 127;
    return $x * pow(2, $exp - 23);
}



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

function hexfloat ($hex){ 
    return (unpack("f", pack('H*',$hex))[1]); 
} 

while( 1 == 1){
    $data = $serial->readPort();
    if(($data != "") && (strlen($data) >= 48)){
        $shift = substr($data,strpos($data, "ff01"),48);
        if(strlen($shift) == 48){
            //echo($shift . "\n");
            $yaw = substr($shift, 38, 2) . substr($shift, 36, 2) . substr($shift, 34, 2) . substr($shift, 32, 2);
            echo($shift . "\n");
            
            echo($yaw . "\n");
            //echo(hex2float($yaw) . "\n");
            //echo(hexdec($yaw) . "\n");
            
            //echo($shift . "\n");
        }
    }   
   usleep(50000);
}

$serial->deviceClose();

?>