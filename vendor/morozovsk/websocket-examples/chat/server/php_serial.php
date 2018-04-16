<?php
/**
 * Serial port control class
 *
 * THIS PROGRAM COMES WITH ABSOLUTELY NO WARANTIES !
 * USE IT AT YOUR OWN RISKS !
 *
 * Changes added by Rizwan Kassim <rizwank@geekymedia.com> for OSX functionality
 * default serial device for osx devices is /dev/tty.serial for machines with a built in serial device
 *
 * @author R�my Sanchez <thenux@gmail.com>
 * @thanks Aur�lien Derouineau for finding how to open serial ports with windows
 * @thanks Alec Avedisyan for help and testing with reading
 * @thanks Jim Wright for OSX cleanup/fixes.
 * @copyright under GPL 2 licence
 */

//  Main class of calculating checksum CRC32.
include_once './crc32/CRC_32_main.php';

class phpSerial{
    public $_device = null;
    public $_windevice = null;
    public $_dHandle = null;
    public $_dState = SERIAL_DEVICE_NOTSET;
    public $_buffer = "";
    public $_os = "";

    /**
     * This var says if buffer should be flushed by sendMessage (true) or manualy (false)
     *
     * @var bool
     */
    public $autoflush = true;

    /**
     * Constructor. Perform some checks about the OS and setserial
     *
     * @return phpSerial
     */
    public function phpSerial ()
    {
        setlocale(LC_ALL, "en_US");

        $sysname = php_uname();
        
        
        if (substr($sysname, 0, 5) === "Linux") {
            $this->_os = "linux";

            if ($this->_exec("stty --version") === 0) {
                register_shutdown_function(array($this, "deviceClose"));
            } else {
                trigger_error("No stty availible, unable to run.", E_USER_ERROR);
            }
        } elseif (substr($sysname, 0, 6) === "Darwin") {
            $this->_os = "osx";
            // We know stty is available in Darwin.
            // stty returns 1 when run from php, because "stty: stdin isn't a
            // terminal"
            // skip this check
            // if($this->_exec("stty") === 0)
            // {
                register_shutdown_function(array($this, "deviceClose"));
            // }
            // else
            // {
            // 	trigger_error("No stty availible, unable to run.", E_USER_ERROR);
            // }
        } elseif (substr($sysname, 0, 7) === "Windows") {
            $this->_os = "windows";
            register_shutdown_function(array($this, "deviceClose"));
        } else {
            trigger_error("Host OS is neither osx, linux nor windows, unable to run.", E_USER_ERROR);
            exit();
        }
    }

    //
    // OPEN/CLOSE DEVICE SECTION -- {START}
    //

    /**
     * Device set function : used to set the device name/address.
     * -> linux : use the device address, like /dev/ttyS0
     * -> osx : use the device address, like /dev/tty.serial
     * -> windows : use the COMxx device name, like COM1 (can also be used
     *     with linux)
     *
     * @param  string $device the name of the device to be used
     * @return bool
     */
    public function deviceSet ($device)
    {
        if ($this->_dState !== SERIAL_DEVICE_OPENED) {
            if ($this->_os === "linux") {
                if (preg_match("@^COM(\d+):?$@i", $device, $matches)) {
                    $device = "/dev/ttyS" . ($matches[1] - 1);
                }

                if ($this->_exec("stty -F " . $device) === 0) {
                    $this->_device = $device;
                    $this->_dState = SERIAL_DEVICE_SET;

                    return true;
                }
            } 

            trigger_error("Specified serial port is not valid", E_USER_WARNING);

            return false;
        } else {
            trigger_error("You must close your device before to set an other one", E_USER_WARNING);

            return false;
        }
    }

    /**
     * Opens the device for reading and/or writing.
     *
     * @param  string $mode Opening mode : same parameter as fopen()
     * @return bool
     */
    public function deviceOpen ($mode = "r+b")
    {
        if ($this->_dState === SERIAL_DEVICE_OPENED) {
            trigger_error("The device is already opened", E_USER_NOTICE);

            return true;
        }

        if ($this->_dState === SERIAL_DEVICE_NOTSET) {
            trigger_error("The device must be set before to be open", E_USER_WARNING);

            return false;
        }

        if (!preg_match("@^[raw]\+?b?$@", $mode)) {
            trigger_error("Invalid opening mode : ".$mode.". Use fopen() modes.", E_USER_WARNING);

            return false;
        }

        $this->_dHandle = @fopen($this->_device, $mode);

        if ($this->_dHandle !== false) {
            stream_set_blocking($this->_dHandle, 0);
            $this->_dState = SERIAL_DEVICE_OPENED;

            return true;
        }

        $this->_dHandle = null;
        trigger_error("Unable to open the device", E_USER_WARNING);

        return false;
    }

    /**
     * Closes the device
     *
     * @return bool
     */
    public function deviceClose ()
    {
        if ($this->_dState !== SERIAL_DEVICE_OPENED) {
            return true;
        }

        if (fclose($this->_dHandle)) {
            $this->_dHandle = null;
            $this->_dState = SERIAL_DEVICE_SET;

            return true;
        }

        trigger_error("Unable to close the device", E_USER_ERROR);

        return false;
    }

    //
    // OPEN/CLOSE DEVICE SECTION -- {STOP}
    //

    //
    // CONFIGURE SECTION -- {START}
    //

    /**
     * Configure the Baud Rate
     * Possible rates : 110, 150, 300, 600, 1200, 2400, 4800, 9600, 38400,
     * 57600 and 115200.
     *
     * @param  int  $rate the rate to set the port in
     * @return bool
     */
    public function confBaudRate ($rate)
    {
        if ($this->_dState !== SERIAL_DEVICE_SET) {
            trigger_error("Unable to set the baud rate : the device is either not set or opened", E_USER_WARNING);

            return false;
        }

        $validBauds = array (
            110    => 11,
            150    => 15,
            300    => 30,
            600    => 60,
            1200   => 12,
            2400   => 24,
            4800   => 48,
            9600   => 96,
            19200  => 19,
            38400  => 38400,
            57600  => 57600,
            115200 => 115200,
            921600 => 921600
        );

        if (isset($validBauds[$rate])) {
            if ($this->_os === "linux") {
                $ret = $this->_exec("stty -F " . $this->_device . " " . (int) $rate, $out);
            }
            if ($this->_os === "osx") {
                $ret = $this->_exec("stty -f " . $this->_device . " " . (int) $rate, $out);
            } elseif ($this->_os === "windows") {
                $ret = $this->_exec("mode " . $this->_windevice . " BAUD=" . $validBauds[$rate], $out);
            } else {
                return false;
            }

            if ($ret !== 0) {
                trigger_error("Unable to set baud rate: " . $out[1], E_USER_WARNING);

                return false;
            }
        }
    }

    /**
     * Configure parity.
     * Modes : odd, even, none
     *
     * @param  string $parity one of the modes
     * @return bool
     */
    public function confParity ($parity)
    {
        if ($this->_dState !== SERIAL_DEVICE_SET) {
            trigger_error("Unable to set parity : the device is either not set or opened", E_USER_WARNING);

            return false;
        }

        $args = array(
            "none" => "-parenb",
            "odd"  => "parenb parodd",
            "even" => "parenb -parodd",
        );

        if (!isset($args[$parity])) {
            trigger_error("Parity mode not supported", E_USER_WARNING);

            return false;
        }

        if ($this->_os === "linux") {
            $ret = $this->_exec("stty -F " . $this->_device . " " . $args[$parity], $out);
        } elseif ($this->_os === "osx") {
            $ret = $this->_exec("stty -f " . $this->_device . " " . $args[$parity], $out);
        } else {
            $ret = $this->_exec("mode " . $this->_windevice . " PARITY=" . $parity{0}, $out);
        }

        if ($ret === 0) {
            return true;
        }

        trigger_error("Unable to set parity : " . $out[1], E_USER_WARNING);

        return false;
    }

    /**
     * Sets the length of a character.
     *
     * @param  int  $int length of a character (5 <= length <= 8)
     * @return bool
     */
    public function confCharacterLength ($int)
    {
        if ($this->_dState !== SERIAL_DEVICE_SET) {
            trigger_error("Unable to set length of a character : the device is either not set or opened", E_USER_WARNING);

            return false;
        }

        $int = (int) $int;
        if ($int < 5) {
            $int = 5;
        } elseif ($int > 8) {
            $int = 8;
        }

        if ($this->_os === "linux") {
            $ret = $this->_exec("stty -F " . $this->_device . " cs" . $int, $out);
        } elseif ($this->_os === "osx") {
            $ret = $this->_exec("stty -f " . $this->_device . " cs" . $int, $out);
        } else {
            $ret = $this->_exec("mode " . $this->_windevice . " DATA=" . $int, $out);
        }

        if ($ret === 0) {
            return true;
        }

        trigger_error("Unable to set character length : " .$out[1], E_USER_WARNING);

        return false;
    }

    /**
     * Sets the length of stop bits.
     *
     * @param float $length the length of a stop bit. It must be either 1,
     * 1.5 or 2. 1.5 is not supported under linux and on some computers.
     * @return bool
     */
    public function confStopBits ($length)
    {
        if ($this->_dState !== SERIAL_DEVICE_SET) {
            trigger_error("Unable to set the length of a stop bit : the device is either not set or opened", E_USER_WARNING);

            return false;
        }

        if ($length != 1 and $length != 2 and $length != 1.5 and !($length == 1.5 and $this->_os === "linux")) {
            trigger_error("Specified stop bit length is invalid", E_USER_WARNING);

            return false;
        }

        if ($this->_os === "linux") {
            $ret = $this->_exec("stty -F " . $this->_device . " " . (($length == 1) ? "-" : "") . "cstopb", $out);
        } elseif ($this->_os === "osx") {
            $ret = $this->_exec("stty -f " . $this->_device . " " . (($length == 1) ? "-" : "") . "cstopb", $out);
        } else {
            $ret = $this->_exec("mode " . $this->_windevice . " STOP=" . $length, $out);
        }

        if ($ret === 0) {
            return true;
        }

        trigger_error("Unable to set stop bit length : " . $out[1], E_USER_WARNING);

        return false;
    }

    /**
     * Configures the flow control
     *
     * @param string $mode Set the flow control mode. Availible modes :
     * 	-> "none" : no flow control
     * 	-> "rts/cts" : use RTS/CTS handshaking
     * 	-> "xon/xoff" : use XON/XOFF protocol
     * @return bool
     */
    public function confFlowControl ($mode)
    {
        if ($this->_dState !== SERIAL_DEVICE_SET) {
            trigger_error("Unable to set flow control mode : the device is either not set or opened", E_USER_WARNING);

            return false;
        }

        $linuxModes = array(
            "none"     => "clocal -crtscts -ixon -ixoff",
            "rts/cts"  => "-clocal crtscts -ixon -ixoff",
            "xon/xoff" => "-clocal -crtscts ixon ixoff"
        );
        $windowsModes = array(
            "none"     => "xon=off octs=off rts=on",
            "rts/cts"  => "xon=off octs=on rts=hs",
            "xon/xoff" => "xon=on octs=off rts=on",
        );

        if ($mode !== "none" and $mode !== "rts/cts" and $mode !== "xon/xoff") {
            trigger_error("Invalid flow control mode specified", E_USER_ERROR);

            return false;
        }

        if ($this->_os === "linux") {
            $ret = $this->_exec("stty -F " . $this->_device . " " . $linuxModes[$mode], $out);
        } elseif ($this->_os === "osx") {
            $ret = $this->_exec("stty -f " . $this->_device . " " . $linuxModes[$mode], $out);
        } else {
            $ret = $this->_exec("mode " . $this->_windevice . " " . $windowsModes[$mode], $out);
        }
        if ($ret === 0) {
            return true;
        } else {
            trigger_error("Unable to set flow control : " . $out[1], E_USER_ERROR);

            return false;
        }
    }

    /**
     * Sets a setserial parameter (cf man setserial)
     * NO MORE USEFUL !
     * 	-> No longer supported
     * 	-> Only use it if you need it
     *
     * @param  string $param parameter name
     * @param  string $arg   parameter value
     * @return bool
     */
    public function setSetserialFlag ($param, $arg = "")
    {
        if (!$this->_ckOpened()) {
            return false;
        }

        $return = exec("setserial " . $this->_device . " " . $param . " " . $arg . " 2>&1");

        if ($return{0} === "I") {
            trigger_error("setserial: Invalid flag", E_USER_WARNING);

            return false;
        } elseif ($return{0} === "/") {
            trigger_error("setserial: Error with device file", E_USER_WARNING);

            return false;
        } else {
            return true;
        }
    }

    //
    // CONFIGURE SECTION -- {STOP}
    //

    //
    // I/O SECTION -- {START}
    //

    /**
     * Sends a string to the device
     *
     * @param string $str          string to be sent to the device
     * @param float  $waitForReply time to wait for the reply (in seconds)
     */
    public function sendMessage ($str, $waitForReply = 0.1)
    {
        $this->_buffer .= $str;

        if ($this->autoflush === true) {
            $this->serialflush();
        }

        usleep((int) ($waitForReply * 1000000));
    }

    /**
     * Reads the port until no new datas are availible, then return the content.
     *
     * @pararm int $count number of characters to be read (will stop before
     * 	if less characters are in the buffer)
     * @return string
     */
    public function readPort($count = 0) {
        if ($this->_dState !== SERIAL_DEVICE_OPENED) {
            trigger_error("Device must be opened to read it", E_USER_WARNING);

            return false;
        }

        if ($this->_os === "linux" || $this->_os === "osx") {
            $package_array = array();   //  Packege by bytes.
            $package_array_str = array();
            $package = "";  //  
            $real_CRC32 = "";

            require './crc32/CRC_32_table.php';    //  Polinom in table view.
            //  This is crutch :-)
            $str2hex = [
                "00" => 0x00, "01" => 0x01, "02" => 0x02, "03" => 0x03, "04" => 0x04, "05" => 0x05, "06" => 0x06, "07" => 0x07, "08" => 0x08, "09" => 0x09, "0A" => 0x0A, "0B" => 0x0B, "0C" => 0x0C, "0D" => 0x0D, "0E" => 0x0E, "0F" => 0x0F,
                "10" => 0x10, "11" => 0x11, "12" => 0x12, "13" => 0x13, "14" => 0x14, "15" => 0x15, "16" => 0x16, "17" => 0x17, "18" => 0x18, "19" => 0x19, "1A" => 0x1A, "1B" => 0x1B, "1C" => 0x1C, "1D" => 0x1D, "1E" => 0x1E, "1F" => 0x1F,
                "20" => 0x20, "21" => 0x21, "22" => 0x22, "23" => 0x23, "24" => 0x24, "25" => 0x25, "26" => 0x26, "27" => 0x27, "28" => 0x28, "29" => 0x29, "2A" => 0x2A, "2B" => 0x2B, "2C" => 0x2C, "2D" => 0x2D, "2E" => 0x2E, "2F" => 0x2F,
                "30" => 0x30, "31" => 0x31, "32" => 0x32, "33" => 0x33, "34" => 0x34, "35" => 0x35, "36" => 0x36, "37" => 0x37, "38" => 0x38, "39" => 0x39, "3A" => 0x3A, "3B" => 0x3B, "3C" => 0x3C, "3D" => 0x3D, "3E" => 0x3E, "3F" => 0x3F,
                "40" => 0x40, "41" => 0x41, "42" => 0x42, "43" => 0x43, "44" => 0x44, "45" => 0x45, "46" => 0x46, "47" => 0x47, "48" => 0x48, "49" => 0x49, "4A" => 0x4A, "4B" => 0x4B, "4C" => 0x4C, "4D" => 0x4D, "4E" => 0x4E, "4F" => 0x4F,
                "50" => 0x50, "51" => 0x51, "52" => 0x52, "53" => 0x53, "54" => 0x54, "55" => 0x55, "56" => 0x56, "57" => 0x57, "58" => 0x58, "59" => 0x59, "5A" => 0x5A, "5B" => 0x5B, "5C" => 0x5C, "5D" => 0x5D, "5E" => 0x5E, "5F" => 0x5F,
                "60" => 0x60, "61" => 0x61, "62" => 0x62, "63" => 0x63, "64" => 0x64, "65" => 0x65, "66" => 0x66, "67" => 0x67, "68" => 0x68, "69" => 0x69, "6A" => 0x6A, "6B" => 0x6B, "6C" => 0x6C, "6D" => 0x6D, "6E" => 0x6E, "6F" => 0x6F,
                "70" => 0x70, "71" => 0x71, "72" => 0x72, "73" => 0x73, "74" => 0x74, "75" => 0x75, "76" => 0x76, "77" => 0x77, "78" => 0x78, "79" => 0x79, "7A" => 0x7A, "7B" => 0x7B, "7C" => 0x7C, "7D" => 0x7D, "7E" => 0x7E, "7F" => 0x7F,
                "80" => 0x80, "81" => 0x81, "82" => 0x82, "83" => 0x83, "84" => 0x84, "85" => 0x85, "86" => 0x86, "87" => 0x87, "88" => 0x88, "89" => 0x89, "8A" => 0x8A, "8B" => 0x8B, "8C" => 0x8C, "8D" => 0x8D, "8E" => 0x8E, "8F" => 0x8F,
                "90" => 0x90, "91" => 0x91, "92" => 0x92, "93" => 0x93, "94" => 0x94, "95" => 0x95, "96" => 0x96, "97" => 0x97, "98" => 0x98, "99" => 0x99, "9A" => 0x9A, "9B" => 0x9B, "9C" => 0x9C, "9D" => 0x9D, "9E" => 0x9E, "9F" => 0x9F,
                "A0" => 0xA0, "A1" => 0xA1, "A2" => 0xA2, "A3" => 0xA3, "A4" => 0xA4, "A5" => 0xA5, "A6" => 0xA6, "A7" => 0xA7, "A8" => 0xA8, "A9" => 0xA9, "AA" => 0xAA, "AB" => 0xAB, "AC" => 0xAC, "AD" => 0xAD, "AE" => 0xAE, "AF" => 0xAF,
                "B0" => 0xB0, "B1" => 0xB1, "B2" => 0xB2, "B3" => 0xB3, "B4" => 0xB4, "B5" => 0xB5, "B6" => 0xB6, "B7" => 0xB7, "B8" => 0xB8, "B9" => 0xB9, "BA" => 0xBA, "BB" => 0xBB, "BC" => 0xBC, "BD" => 0xBD, "BE" => 0xBE, "BF" => 0xBF,
                "C0" => 0xC0, "C1" => 0xC1, "C2" => 0xC2, "C3" => 0xC3, "C4" => 0xC4, "C5" => 0xC5, "C6" => 0xC6, "C7" => 0xC7, "C8" => 0xC8, "C9" => 0xC9, "CA" => 0xCA, "CB" => 0xCB, "CC" => 0xCC, "CD" => 0xCD, "CE" => 0xCE, "CF" => 0xCF,
                "D0" => 0xD0, "D1" => 0x71, "D2" => 0xD2, "D3" => 0xD3, "D4" => 0xD4, "D5" => 0xD5, "D6" => 0xD6, "D7" => 0xD7, "D8" => 0xD8, "D9" => 0xD9, "DA" => 0xDA, "DB" => 0xDB, "DC" => 0xDC, "DD" => 0xDD, "DE" => 0xDE, "DF" => 0xDF,
                "E0" => 0xE0, "E1" => 0xE1, "E2" => 0xE2, "E3" => 0xE3, "E4" => 0xE4, "E5" => 0xE5, "E6" => 0xE6, "E7" => 0xE7, "E8" => 0xE8, "E9" => 0xE9, "EA" => 0xEA, "EB" => 0xEB, "EC" => 0xEC, "ED" => 0xED, "EE" => 0xEE, "EF" => 0xEF,
                "F0" => 0xF0, "F1" => 0xF1, "F2" => 0xF2, "F3" => 0xF3, "F4" => 0xF4, "F5" => 0xF5, "F6" => 0xF6, "F7" => 0xF7, "F8" => 0xF8, "F9" => 0xF9, "FA" => 0xFA, "FB" => 0xFB, "FC" => 0xFC, "FD" => 0xFD, "FE" => 0xFE, "FF" => 0xFF
            ];

            //  Read port while packege length < 56.
            while (strlen($package) !== 56) {
                $data = bin2hex(fread($this->_dHandle, 1));
                if ($data == "ff") {
                    while (strlen($package) !== 56) {
                        if ($data !== null && $data !== "") {
                            $package .= $data;
                            if (strlen($package) <= 48) {
                                array_push($package_array, $str2hex[strtoupper($data)]);
                                array_push($package_array_str, $data);
                            } else {
                                $real_CRC32 = $data . $real_CRC32;
                            }
                        }
                        $data = bin2hex(fread($this->_dHandle, 1));
                    }
                }
            }

            $crc32 = new Crc32();
            //  Calculating checksum CRC32.
            $CrcResult_ = $crc32->ComputeCrc($CRC_32_, $package_array);
            if ($real_CRC32 == dechex($CrcResult_->Crc)) {
                return($package_array_str);
                //return($package_array); // Return full confirmed packege.
            }else{
                return false;
            }
            //return($package . "\n" . $package_HEX . "\n" . dechex($CrcResult_->Crc) . "\n" . $real_CRC32 . "\n");   
        }
        return false;
    }

    /**
     * Flushes the output buffer
     * Renamed from flush for osx compat. issues
     *
     * @return bool
     */
    public function serialflush ()
    {
        if (!$this->_ckOpened()) {
            return false;
        }

        if (fwrite($this->_dHandle, $this->_buffer) !== false) {
            $this->_buffer = "";

            return true;
        } else {
            $this->_buffer = "";
            trigger_error("Error while sending message", E_USER_WARNING);

            return false;
        }
    }

    //
    // I/O SECTION -- {STOP}
    //

    //
    // INTERNAL TOOLKIT -- {START}
    //

    public function _ckOpened()
    {
        if ($this->_dState !== SERIAL_DEVICE_OPENED) {
            trigger_error("Device must be opened", E_USER_WARNING);

            return false;
        }

        return true;
    }

    public function _ckClosed()
    {
        if ($this->_dState !== SERIAL_DEVICE_CLOSED) {
            trigger_error("Device must be closed", E_USER_WARNING);

            return false;
        }

        return true;
    }

    public function _exec($cmd, &$out = null)
    {
        $desc = array(
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );

        $proc = proc_open($cmd, $desc, $pipes);

        $ret = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $retVal = proc_close($proc);

        if (func_num_args() == 2) {
            $out = array($ret, $err);
        }

        return $retVal;
    }

    //
    // INTERNAL TOOLKIT -- {STOP}
    //
}
