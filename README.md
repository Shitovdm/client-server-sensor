# client-server-sensor

This client-server application is designed to visualize data from the sensor on the client device.

### Generalized structural scheme
![screen](https://raw.githubusercontent.com/Shitovdm/client-server-sensor/master/service/img/Scheme-1.PNG)

### Detailed structural scheme
![screen](https://raw.githubusercontent.com/Shitovdm/client-server-sensor/master/service/img/Scheme-2.PNG)


You should have Raspberry Pi 3 with installed system ubuntu-16.04 or last version of Raspbian. 

---

<h3>Ubuntu-16.04</h3>

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

<h3>Possible issues:</h3>

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

---

<h3>Raspbian</h3>

<h3>Server Start</h3>

1. Install LAMP Server:  
```sudo apt-get install apache2 -y```  
```sudo a2enmod rewrite```  
```sudo service apache2 restart``` 
```sudo nano /etc/apache2/apache2.conf```  
```  
<Directory /var/www/>  
 Options Indexes FollowSymLinks  
 AllowOverride All  
 Require all granted  
</Directory>  
```  
```sudo service apache2 restart```  

 2. Install PHP:  
 ```sudo apt-get install php libapache2-mod-php -y```  
 
 3. Setup FTP:  
 ```sudo apt-get install vsftpd -y```  
 ```sudo nano /etc/vsftpd.conf```  
 ```  
 #local_enable=YES  
 #ssl_enable=NO  
 # CUSTOM  
 ssl_enable=YES  
 local_enable=YES  
 chroot_local_user=YES  
 local_root=/var/www  
 user_sub_token=pi  
 write_enable=YES  
 local_umask=002  
 allow_writeable_chroot=YES  
 ftpd_banner=Welcome to my Raspberry Pi FTP service.  
 ```  
 ```sudo usermod -a -G www-data pi```  
 ```sudo usermod -m -d /var/www pi```  
 ```sudo chown -R www-data:www-data /var/www```  
 ```sudo chmod -R 775 /var/www```  
 ```sudo service vsftpd restart```  
 
 **Use to connect:**  
 - Host – 192.xxx.x.xxx (IP address of your Pi with no prefix)  
 - Port – 21  
 - Protocol – FTP (File Transfer Protocol)  
 - Encryption – Use explicit FTP over TLS if available  
 - Logon Type – Normal (username & password)  
 - Username – pi  
 - Password – yourPass  

<h3>Wi-Fi Hotspot Setup</h3>

1. Install Raspap:  
```wget -q https://git.io/voEUQ -O /tmp/raspap && bash /tmp/raspap```  

2. Install hostapd:  
```sudo apt-get install hostapd isc-dhcp-server```  

3. Customize interfaces.d:  
``` sudo nano /etc/network/interfaces.d```  
```
auto lo
iface lo inet loopback
auto eth0
allow-hotplug eth0
iface eth0 inet dhcp
allow-hotplug wlan0
iface wlan0 inet static
address 10.3.141.1
netmask 255.255.255.0
```

<h3>Set up auto-update</h3>  

1. Change permissions on all files and folders:  
```sudo chmod -R 777 var/www/html```  

2. Change owner for all files and folders in server dir:  
```cd /var/www/html```  
```sudo chmod www-data:www-data .```  

3. Create new local repo in ```var/www/html/upgrade```  

<h3>Important</h3>  
Do not forget to rename default ```/var/www/html``` to arbitrary name.  
By default ```/var/www/html``` contain Raspap GUI web-interface.  
Create new html folder and place all files from whis repo.  

<h3>Issues</h3>

1. Uncaught Error: Call to undefined mb_check_encoding  
**Solution:**  
```
sudo apt-get install php7.0-mbstring
sudo service apache2 restart
```

---

After all the manipulations, you should get in ```var/www/html``` a similar structure:
```
client  
  css  
  img  
  js  
  svg  
service  
  bash  
  img  
  php  
upgrade  
  html  
  serial  
  service  
  update.php
vendor  
  composer  
  morozovsk  
  autoload.php  
gkv_udp_send  
index.html  
serial_start.sh  
```  

**To start the autorun, edit the file ```/etc/rc.local```**  
```
#!/bin/sh -e
#
# rc.local
#
# This script is executed at the end of each multiuser runlevel.
# Make sure that the script will "exit 0" on success or any other
# value on error.
#
# In order to enable or disable this script just change the execution
# bits.
#
# By default this script does nothing.

# Print the IP address
_IP=$(hostname -I) || true
if [ "$_IP" ]; then
  printf "My IP address is %s\n" "$_IP"
fi

echo 1 > /proc/sys/net/ipv4/ip_forward #RASPAP
iptables -t nat -A POSTROUTING -j MASQUERADE #RASPAP
sudo bash /var/www/html/service/bash/shutdown.sh &
cd /var/www/html/vendor/morozovsk/websocket-examples/chat/server
php index.php start &
exit 0
```  
---

<h3>Client</h3>

1. Connect to access point.  
2. Go to local server address(http://192.168.1.41).  

### Interface

![screen](https://raw.githubusercontent.com/Shitovdm/client-server-sensor/master/service/img/interface-2.png)  

![screen](https://raw.githubusercontent.com/Shitovdm/client-server-sensor/master/service/img/client-r.png)  

**Explanations:**
1. Units (grad/min).  
2. Change the measurement limit of the instrument(range: 30-300, every 15).  
3. Digital indicator of the exact value.  
4. Settings button.  
5. Save all in log button.  
6. Current measurement limit.  

---

<h3>Resources:</h3>  

>> **https://github.com/morozovsk/websocket** - PHP Websocket Class.  
>> **https://github.com/Xowap/PHP-Serial** - PHP Serial Class.  
>> **https://github.com/hongkiat/svg-meter-gauge** - Simple SVG-meter.  
>> **https://github.com/meetanthony/crcphp** - Calculating CRC32 Class.  
>> **https://wiki.ubuntu.com/ARM/RaspberryPi** - ubuntu-18.04-preinstalled-server-armhf+raspi3.img.xz (4G image, 295M compressed).  
>> **http://academicfox.com/raspberry-pi-besprovodnaya-tochka-dostupa-wifi-access-point/** - Hotspot on RasPi3.
>> **https://github.com/billz/raspap-webgui** - Hotspot RPI Raspbian.  
>> **https://howtoraspberrypi.com/create-a-wi-fi-hotspot-in-less-than-10-minutes-with-pi-raspberry/** - Hotspot RPI Raspbian.
