<?php

namespace morozovsk\websocket\examples\chat\server;

//пример реализации чата
class ChatWebsocketDaemonHandler extends \morozovsk\websocket\Daemon
{
    
    protected $data = array();
    protected $counter = 0;
    protected $direction = false;
    protected $demon_enable = false;
    
    
    /*protected function demon(){
        $tstart = time();
        $howlongmywork = $tstart - time();
        while (1 == 1) {
            usleep(500000);
            echo(time());
            $howlongmywork = $tstart - time();
        }

        /*while(1 == 1){
            $tstart             =   time();

            $howlongmywork      =   $tstart - time();

            while ($howlongmywork <= 10) {

                file_put_contents(rand().'.txt', 'test');

                $howlongmywork  =   $tstart - time();
            }
            echo($this->counter);
            if( ($this->counter >= -30) && (!$this->direction) ){
                $this->counter = round($this->counter - 0.1, 1);
            }else{
                if($this->counter <= 30){
                    $this->counter = round($this->counter + 0.1, 1);
                    $this->direction = true;
                }else{
                    $this->direction = false;
                }
            }
            foreach ($this->clients as $clientId => $client) {
                $this->sendToClient($clientId, $this->counter);
            }
            
        }
        
    }*/
    
    protected function onOpen($connectionId, $info) {//вызывается при соединении с новым клиентом
        /**/
        /*if($this->demon_enable == false){
            $this->demon_enable = true;
            $this->demon();
        }*/
    }
    

    protected function onClose($connectionId) {//вызывается при закрытии соединения с существующим клиентом

    }
    

    protected function onMessage($connectionId, $data, $type) {//вызывается при получении сообщения от клиента
        if (!strlen($data)) {
            return;
        }
        /*if($data !== "0"){
            $message = $this->counter;
            echo($data . " " . $message . " ");
            
            foreach ($this->clients as $clientId => $client) {
                $this->sendToClient($clientId, $message);
            }
            if( ($this->counter >= -30) && (!$this->direction) ){
                $this->counter = round($this->counter - 0.1, 1);
            }else{
                if($this->counter <= 30){
                    $this->counter = round($this->counter + 0.1, 1);
                    $this->direction = true;
                }else{
                    $this->direction = false;
                }
            }
            
        }*/
        
    }
    protected function onMasterClose($connectionId) {}
}