<?php
namespace morozovsk\websocket\examples\chat\server;
define("SERIAL_DEVICE_NOTSET", 0);
define("SERIAL_DEVICE_SET", 1);
define("SERIAL_DEVICE_OPENED", 2);
include "php_serial.php";

//  Main class of calculating checksum CRC32.
include_once './crc32/CRC_32_main.php';




class OpenCOM
{
    function __construct() {
         echo("Port opened. \n");
         $serial = new phpSerial(); //  Экземпляр класса работы с портом.
         $serial->phpSerial();  //  Вызываем конструктор явно.
         $serial->deviceSet("/dev/ttyUSB0");
         $serial->confBaudRate(115200);
         $serial->confParity("none");
         $serial->confCharacterLength(8);
         $serial->confStopBits(1);
         $serial->confFlowControl("none");
         $serial->deviceOpen();
    }
    /*function __destruct() {
        $serial->deviceClose();
    }*/
}

class ChatWebsocketDaemonHandler extends \morozovsk\websocket\Daemon
{
    
    protected $data = array();
    protected $counter = 0;
    protected $direction = false;
    protected $demon_enable = false;
    public $COM;
    

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
        //  Выполняем до тех пор, пока не будет получены корректные данные.
        while( 1 === 1){
            $data = $this->COM->readPort(); //  Получаем верифицированный пакет.
            if($data !== false){    //  Если пакет успешно получен.
                //  Достаем нужные 4 байта.
                $angularVelocity = round($this->hex2float($data[23] . $data[22] . $data[21] . $data[20]), 2);
                echo($angularVelocity . "\n");
                break;  //  Выходим из петли, т.к. данные получены успешно.
            }else{
                echo("Loop...");
            }
        }
        return $angularVelocity;
    }
    
    
    protected function transferDemon(){
        echo("transfer.. \n");
        $angle = $this->readingData();
        foreach ($this->clients as $clientId => $client) {
            $this->sendToClient($clientId, $angle);
        }
        
        //$this->transferDemon();
    }
    
    public function onOpen($connectionId, $info) {//вызывается при соединении с новым клиентом
        // Открываем порт и сообщаем об этом клиенту.
        $this->COM = new phpSerial(); //  Экземпляр класса работы с портом.
        $this->COM->phpSerial();  //  Вызываем конструктор явно.
        $this->COM->deviceSet("/dev/ttyUSB0");
        $this->COM->confBaudRate(115200);
        $this->COM->confParity("none");
        $this->COM->confCharacterLength(8);
        $this->COM->confStopBits(1);
        $this->COM->confFlowControl("none");
        $this->COM->deviceOpen();
        echo("Port opened. \n");

        foreach ($this->clients as $clientId => $client) {
            $this->sendToClient($clientId, "COM port on server opened.");
        }
        echo("Client " . $clientId . " connect!");
    }

    protected function onClose($connectionId) {//вызывается при закрытии соединения с существующим клиентом
    }
    
    protected function onMessage($connectionId, $data, $type) {//вызывается при получении сообщения от клиента
        if (!strlen($data)) {
            return;
        }
        if($data !== "0"){
            $this->transferDemon();
        }
    }
    
    protected function onMasterClose($connectionId) {
        
    }
}
