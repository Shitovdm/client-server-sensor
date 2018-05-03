<?php
namespace morozovsk\websocket\examples\chat\server;

define("SERIAL_DEVICE_NOTSET", 0);
define("SERIAL_DEVICE_SET", 1);
define("SERIAL_DEVICE_OPENED", 2);

class ChatWebsocketDaemonHandler extends \morozovsk\websocket\Daemon
{
    
    protected $data = array();
    protected $counter = 0;
    protected $direction = false;
    protected $demon_enable = false;
    protected $serial_start = false;
    
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
    
    /**
     * Читает порт, находит целый пакет и отправляет пользователям.
     * @return boolean
     */
    public function readANDtransfer() {
        //  Достаем данныые из сокета, передаем клиенту.
        $socket = stream_socket_server("udp://127.0.0.1:1234", $errno, $errstr, STREAM_SERVER_BIND);
        if (!$socket) {
            echo("Error". $errno . "\n");
            return false;
            die("$errstr ($errno)");
        }

        do {
            $pkt = stream_socket_recvfrom($socket, 4, 0);
            $inv_data = bin2hex($pkt)[6].bin2hex($pkt)[7].bin2hex($pkt)[4].bin2hex($pkt)[5].bin2hex($pkt)[2].bin2hex($pkt)[3].bin2hex($pkt)[0].bin2hex($pkt)[1];

            $angularVelocity = round($this->hex2float($inv_data), 2);
            //  echo $angularVelocity . "\n";
            $this->toClients($angularVelocity); //  Отправка данных клиентам.
            return $angularVelocity;

        } while ($pkt !== false);

        return false;
    }
    
    /**
     * Отправляет значение $value клиентам.
     * @param type $value
     * @return boolean
     */
    public function toClients($value){
        foreach ($this->clients as $clientId => $client) {
            $this->sendToClient($clientId, $value);
            if($clientId == end($this->clients)) {
                return true;
            }
        }
    }

    //  Вызывается при соединении с новым клиентом
    public function onOpen($connectionId, $info) { 
        //  Программа чтения данных с порта стартует при первом подключении клиента и продолжает работать демоном.
        if($this->serial_start == false){
            $this->serial_start = true;
            echo("Opening serial... \n");
            exec("./serial_start.sh > /dev/null 2>&1 &");
        }else{
            echo("Serial is open. \n");
        }
        
        //  Уведомляем клинтов, что открыт порт.
        foreach ($this->clients as $clientId => $client) {
            $this->sendToClient($clientId, "COM-порт на сервере открыт.");
        }
        echo("Client " . $clientId . " connect! \n");
        echo("Transferring data... \n");
        //  Читаем и отправляем первый пакет.
        $this->readANDtransfer();
    }
    
    protected function onClose($connectionId) {//вызывается при закрытии соединения с существующим клиентом
        echo("Client disconnected. \n");
    }
    
    //  Вызывается по получении нового сообщения.
    protected function onMessage($connectionId, $data, $type) {//вызывается при получении сообщения от клиента
        if (!strlen($data)) {
            return;
        }
        if($data !== "0"){
            $this->readANDtransfer();
        }
    }
    
    protected function onMasterClose($connectionId) {
        
    }
}
