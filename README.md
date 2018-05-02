# client-server-sensor

This client-server application is designed to visualize data from the sensor on the client device.

### Generalized structural scheme
![screen](https://raw.githubusercontent.com/Shitovdm/client-server-sensor/master/service/img/Scheme-1.PNG)

### Detailed structural scheme
![screen](https://raw.githubusercontent.com/Shitovdm/client-server-sensor/master/service/img/Scheme-2.PNG)


You should have Raspberry Pi 3 with installed system ubuntu-16.04. 

<h3>Server Start</h3>

**Server installation lasts a fairly long time, about 30-40 minutes.** 

1. Use ubuntu-16.04 (ubuntu-16.04-preinstalled-server-armhf+raspi3.img);
2. Install LAMP:  
```sudo apt-get install lamp-server^ phpmyadmin```  
```sudo /etc/init.d/apache2 start```  
```sudo chmod -R 777 /var/www```  
3. Install FTP-server:  
```sudo apt-get install vsftpd```  
```sudo systemctl start vsftpd```  
```sudo systemctl enable vsftpd```  
```sudo ufw allow 20/tcp```  
```sudo ufw allow 21/tcp```  
```sudo cp /etc/vsftpd.conf /etc/vsftpd.conf.orig```  
```sudo nano /etc/vsftpd.conf```  
4. Edit ftp config /etc/vsftpd.conf:  
```
anonymous_enable = NO  
local_enable = YES  
write_enable = YES  
local_umask = 022  
dirmessage_enable = YES  
xferlog_enable = YES  
xferlog_std_format=YES  
connect_from_port_20 = YES  
listen=YES  
listen_ipv6=NO  
pam_service_name=vsftpd  
userlist_enable = YES  
userlist_file=/etc/vsftpd.userlist  
userlist_deny=NO  
```  
5. Create new user:  
```sudo useradd -m -c "mplab" -s /bin/bash mplab```  
```sudo passwd mplab```  
```echo "mplab" | sudo tee -a /etc/vsftpd.userlist```  
```cat /etc/vsftpd.userlist```  
6. Restart FTP-server;  
7. Paste all project files from ./app into /var/www/html;  
8. Start LAMP:  
```sudo /etc/init.d/apache2 start```  
9. Move to server/index.php folder and start php server:  
```cd /var/www/html/sensor/vendor/morozovsk/websocket-examples/chat/server/```  
```php index.php start``` 

<h3>Wi-Fi Hotspot Setup</h3>

1. Install hostapd:  
```sudo apt-get install hostapd isc-dhcp-server```  
2. DHCP-server settings:  
```sudo nano /etc/dhcp/dhcpd.conf```  
```
#option domain-name "example.org";  
#option domain-name-servers ns1.example.org, ns2.example.org;  
authoritative;  
subnet 192.168.42.0 netmask 255.255.255.0 {  
  range 192.168.42.10 192.168.42.50;  
  option broadcast-address 192.168.42.255;  
  option routers 192.168.42.1;  
  default-lease-time 600;
  max-lease-time 7200;  
  option domain-name "local";  
  option domain-name-servers 8.8.8.8, 8.8.4.4;  
}
```  
3. Edit isc-dhcp-server:
```sudo nano /etc/default/isc-dhcp-server```  
```
INTERFACES=”wlan0″
```  
4. Static ip configuration:
```sudo ifdown wlan0```  
```sudo nano /etc/network/interfaces```  
```
#auto wlan0
allow-hotplug wlan0  
iface wlan0 inet static  
address 192.168.42.1  
netmask 255.255.255.0  
```  
```sudo ifconfig wlan0 192.168.42.1```  

5. WI-FI config:  
```sudo nano /etc/hostapd/hostapd.conf```   
```
interface=wlan0
driver=nl80211
ssid=PI_sensor_v1
hw_mode=g
channel=6
macaddr_acl=0
auth_algs=1
ignore_broadcast_ssid=0
wpa=2
wpa_passphrase=mplabsensor
wpa_key_mgmt=WPA-PSK
wpa_pairwise=TKIP
rsn_pairwise=CCMP
```  
```sudo nano /etc/default/hostapd```  
```
DAEMON_CONF=”/etc/hostapd/hostapd.conf”  
```  

6. NAT settings:  
```sudo nano /etc/sysctl.conf```  
```
net.ipv4.ip_forward=1  
```
```sudo sh -c "echo 1 > /proc/sys/net/ipv4/ip_forward"```  
```sudo iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE```  
```sudo iptables -A FORWARD -i eth0 -o wlan0 -m state --state RELATED,ESTABLISHED -j ACCEPT```  
```sudo iptables -A FORWARD -i wlan0 -o eth0 -j ACCEPT```  
```sudo sh -c "iptables-save > /etc/iptables.ipv4.nat"```  
```sudo nano /etc/network/interfaces```  
```
up iptables-restore < /etc/iptables.ipv4.nat  
```  

7. Updata hostapd(optional):  
```wget http://adafruit-download.s3.amazonaws.com/adafruit_hostapd_14128.zip```  
```unzip adafruit_hostapd_14128.zip```  
```sudo mv /usr/sbin/hostapd /usr/sbin/hostapd.ORIG```  
```sudo mv hostapd /usr/sbin```  
```sudo chmod 755 /usr/sbin/hostapd```  

8. To startup:  
```sudo service hostapd start```  
```sudo service isc-dhcp-server start```  
```sudo update-rc.d hostapd enable```  
```sudo update-rc.d isc-dhcp-server enable```  

9. Other:
```sudo /usr/sbin/hostapd /etc/hostapd/hostapd.conf``` - start hostapd.  
```sudo service hostapd status``` - hostapd status.  
```sudo service isc-dhcp-server status``` - isc-dhcp-server status.  

<h3>Client</h3>

1. Connect to Connector to access point.  
2. Go to local server address(http://192.168.1.41).  


<h3>Set up auto-update</h3>  
1. Change permissions on all files and folders:  
```
sudo chmod -R 777 var/www/html
```

### Interface
![screen](https://raw.githubusercontent.com/Shitovdm/client-server-sensor/master/service/img/client-r.png)  

**Explanations:**
1. Units (grad/min).  
2. Change the measurement limit of the instrument(range: 30-300, every 15).  
3. Digital indicator of the exact value.  
4. Settings button.  
5. Save all in log button.  
6. Current measurement limit.  

<h3>Possible problems:</h3>

1. Timeout for connection search eth0 in 5 minutes at system start.  
**Solution**:  
```nano /lib/systemd/system/networking.service```   
```
TimeoutStartSec = 5sec  
```  

2. You must enter a password to get started.  
**Solution:**  
```nano /etc/systemd/system/getty.target.wants/getty@tty1.service```   
```
ExecStart=-/sbin/agetty --autologin mplab --noclear %I $TERM  
```  

3. Adding the websocket server script to startup.  
```nano /etc/rc.local```  
Before "exit 0" add:  
```
cd /var/www/html/vendor/morozovsk/websocket-examples/chat/server  
php /var/www/html/vendor/morozovsk/websocket-examples/chat/server/index.php start &  
```  

4. Adding turn on button (https://geektimes.ru/post/255098/).  
```nano /etc/rc.local```  
Before "exit 0" add:  
```
sudo bash /home/mplab/sys/shutdown.sh 
```  

5. Fixing recursive fault but reboot is needed.  
**No solution!**  

6. Problem with in-build UART (http://raspberrypi.ru/blog/627.html).  
**Solution:**  
Use external adapter CP2102 UART<->USB.  

<h3>Resources:</h3>  

>> **https://github.com/morozovsk/websocket** - PHP Websocket Class.  
>> **https://github.com/Xowap/PHP-Serial** - PHP Serial Class.  
>> **https://github.com/hongkiat/svg-meter-gauge** - Simple SVG-meter.  
>> **https://github.com/meetanthony/crcphp** - Calculating CRC32 Class.  
>> **https://wiki.ubuntu.com/ARM/RaspberryPi** - ubuntu-18.04-preinstalled-server-armhf+raspi3.img.xz (4G image, 295M compressed).  
>> **http://academicfox.com/raspberry-pi-besprovodnaya-tochka-dostupa-wifi-access-point/** - Hotspot on RasPi3.



Connection to UDP socket:
***
$socket = stream_socket_server("udp://127.0.0.1:1234", $errno, $errstr, STREAM_SERVER_BIND);
if (!$socket) {
    die("$errstr ($errno)");
}

do {
    $pkt = stream_socket_recvfrom($socket, 1, 0, $peer);
    echo $pkt."\n";
    //stream_socket_sendto($socket, date("D M j H:i:s Y\r\n"), 0, $peer);
} while ($pkt !== false);
***  
Test use
