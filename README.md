# client-server-sensor

This client-server application is designed to visualize data from the sensor on the client device.

### Generalized structural scheme
![screen](https://raw.githubusercontent.com/Shitovdm/client-server-sensor/master/service/img/Scheme-1.PNG)

***  

### Detailed structural scheme
![screen](https://raw.githubusercontent.com/Shitovdm/client-server-sensor/master/service/img/Scheme-2.PNG)

***

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
8. Edit ftp config /etc/vsftpd.conf:  
```anonymous_enable = NO```  
```local_enable = YES```  
```write_enable = YES```  
```local_umask = 022```  
```dirmessage_enable = YES```  
```xferlog_enable = YES```  
```xferlog_std_format=YES```  
```connect_from_port_20 = YES```  
```listen=YES```  
```listen_ipv6=NO```  
```pam_service_name=vsftpd```  
```userlist_enable = YES```  
```userlist_file=/etc/vsftpd.userlist```  
```userlist_deny=NO```  
9. Create new user:  
```sudo useradd -m -c "mplab" -s /bin/bash mplab```  
```sudo passwd mplab```  
```echo "mplab" | sudo tee -a /etc/vsftpd.userlist```  
```cat /etc/vsftpd.userlist```  
4. Restart FTP-server;  
5. Paste all project files into /var/www/html;  
6. Start LAMP:  
```sudo /etc/init.d/apache2 start```  
7. Move to server/index.php folder and start php server:  
```cd /var/www/html/sensor/vendor/morozovsk/websocket-examples/chat/server/```  
```php index.php start``` 

<h3>Wi-Fi Access Point Setup</h3>

1. Install libpam-radius-auth:
```sudo apt-get install hostapd libpam-radius-auth```  

> Links
>> http://linux-user.ru/distributivy-linux/programmy-dlya-linux/lokalnyj-server-lamp-dlya-ubuntu-linux-mint/
>> http://rus-linux.net/MyLDP/server/ftp.html
>> https://losst.ru/ustanovka-ftp-na-ubuntu-16-04


<h3>Client</h3>

1. Go to local server address
For example http://192.168.1.41

### Client interface
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
Edit row:  
```TimeoutStartSec = 5sec```  

2. You must enter a password to get started.  
**Solution:**  
```nano /etc/systemd/system/getty.target.wants/getty@tty1.service```  
Edit row:  
```ExecStart=-/sbin/agetty --autologin mplab --noclear %I $TERM```  

3. Adding the websocket server script to startup.
```nano /etc/rc.local```  
Before "exit 0" add:  
```
cd /var/www/html/vendor/morozovsk/websocket-examples/chat/server
php /var/www/html/vendor/morozovsk/websocket-examples/chat/server/index.php start &  
```  
4. Adding turn on button (https://geektimes.ru/post/255098/).  

5. Fixing recursive fault but reboot is needed.
**No solution!**

6. Problem with in-build UART (http://raspberrypi.ru/blog/627.html).
**Solution:**  
Use external adapter CP2102 UART<->USB.
***

> **Resources**:
>> **https://github.com/morozovsk/websocket** - PHP Websocket Class.  
>> **https://github.com/Xowap/PHP-Serial** - PHP Serial Class.  
>> **https://github.com/hongkiat/svg-meter-gauge** - Simple SVG-meter.  
>> **https://github.com/meetanthony/crcphp** - Calculating CRC32 Class.  
>> **https://wiki.ubuntu.com/ARM/RaspberryPi** - ubuntu-18.04-preinstalled-server-armhf+raspi3.img.xz (4G image, 295M compressed).  
>> **http://academicfox.com/raspberry-pi-besprovodnaya-tochka-dostupa-wifi-access-point/** - Hotspot on RasPi3.

  
