<?php

namespace morozovsk\websocket\examples\chat\server;

define("SERIAL_DEVICE_NOTSET", 0);
define("SERIAL_DEVICE_SET", 1);
define("SERIAL_DEVICE_OPENED", 2);
include "php_serial.php";

class ChatWebsocketDaemonHandler extends \morozovsk\websocket\Daemon
{
    
    protected $data = array();
    protected $counter = 0;
    protected $direction = false;
    protected $demon_enable = false;
    
    /**
     * Worked converting method.
     * @param hex $number
     * @return float
     */
    protected function hex2float($number) {
        $binfinal = sprintf("%032b", hexdec($number));
        $sign = substr($binfinal, 0, 1);
        $exp = substr($binfinal, 1, 8);
        $mantissa = "1" . substr($binfinal, 9);
        $mantissa = str_split($mantissa);
        $exp = bindec($exp) - 127;
        $significand = 0;
        for ($i = 0; $i < 24; $i++) {
            $significand += (1 / pow(2, $i)) * $mantissa[$i];
        }
        return $significand * pow(2, $exp) * ($sign * -2 + 1);
    }

    protected function readingData() {
        include 'serial.php';
        return $angularVelocity;
    }

    protected function onOpen($connectionId, $info) {//вызывается при соединении с новым клиентом
         foreach ($this->clients as $clientId => $client) {
            $this->sendToClient($clientId, "1");
        }
        echo("Client ".$clientId." connect!");
    }
    

    protected function onClose($connectionId) {//вызывается при закрытии соединения с существующим клиентом

    }
    

    protected function onMessage($connectionId, $data, $type) {//вызывается при получении сообщения от клиента
        if (!strlen($data)) {
            return;
        }
        if($data !== "0"){
            $angle = $this->readingData();
            foreach ($this->clients as $clientId => $client) {
                $this->sendToClient($clientId, $angle);
            }
        }
    }
    
    protected function onMasterClose($connectionId) {
        
    }
}