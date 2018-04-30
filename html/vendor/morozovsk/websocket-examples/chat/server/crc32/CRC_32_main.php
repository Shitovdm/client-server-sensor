<?php
namespace morozovsk\websocket\examples\chat\server;
class CrcResult {
    public $Name = '';
    public $Crc = 0;
}

class CrcParams {
    public $Name;
    public $Array;
    public $Init;
    public $XorOut;
    public $RefOut;
    public $Poly;
    public $RefIn;
    public $Check;
    public $InvertedInit;
}

class Crc32 {

    public function ComputeCrc($crcParams, $data) {
        $crc = $crcParams->Init;
        if ($crcParams->RefOut) {
            foreach ($data as $d) {
                $crc = $crcParams->Array[($d ^ $crc) & 0xFF] ^ ($crc >> 8 & 0xFFFFFF);
            }
        } else {
            foreach ($data as $d) {
                $crc = $crcParams->Array[(($crc >> 24) ^ $d) & 0xFF] ^ ($crc << 8);
            }
        }
        $crc = $crc ^ $crcParams->XorOut;
        $result = new CrcResult();
        $result->Crc = $crc;
        return $result;
    }

}







