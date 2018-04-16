<?php

$serial = new phpSerial;
$serial->deviceSet("/dev/ttyUSB0");
$serial->confBaudRate(115200);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(1);
$serial->confFlowControl("none");

$serial->deviceOpen();
$isOpen = true;

$angle = 0;
//  Loop reading data.

while( 1 === 1){
    $data = $serial->readPort();
    if($data !== false){    //  Если пакет успешно получен.
        $angularVelocity = round($this->hex2float($data[23] . $data[22] . $data[21] . $data[20]), 2);
        //$angularVelocity = hexTo32Float();
        echo($angularVelocity . "\n");
        break;
    }
}

$serial->deviceClose();
?>