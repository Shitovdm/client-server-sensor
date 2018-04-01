<?php

namespace morozovsk\websocket\examples\chat\server;

//пример реализации чата
class ChatWebsocketDaemonHandler extends \morozovsk\websocket\Daemon
{
    
    protected $data = array();
    protected $counter = 0;
    
    protected function onOpen($connectionId, $info) {//вызывается при соединении с новым клиентом
        
        
    }
    

    
    protected function onClose($connectionId) {//вызывается при закрытии соединения с существующим клиентом

    }
    

    protected function onMessage($connectionId, $data, $type) {//вызывается при получении сообщения от клиента
        if (!strlen($data)) {
            return;
        }
        $message = $this->counter++;
        

        foreach ($this->clients as $clientId => $client) {
            $this->sendToClient($clientId, $message);
        }
    }
    protected function onMasterClose($connectionId) {}
}