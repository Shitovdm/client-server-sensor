# client-server-sensor

This client-server application is designed to visualize data from the sensor on the client device.

### Generalized structural scheme
![screen](https://raw.githubusercontent.com/Shitovdm/client-server-sensor/master/service/img/Scheme-1.PNG)

***  

### Detailed structural scheme
![screen](https://raw.githubusercontent.com/Shitovdm/client-server-sensor/master/service/img/Scheme-2.PNG)

***

<h3>Server Start</h3>

1. Use ubuntu-16.04.4-desktop-i386;  
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
8. Edit ib /etc/vsftpd.conf:  
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
```sudo useradd -m -c "Test User" -s /bin/bash testuser```  
```sudo passwd testuser```  
```echo "testuser" | sudo tee -a /etc/vsftpd.userlist```  
```cat /etc/vsftpd.userlist```  
4. Restart FTP-server;  
5. Paste all project files into /var/www/html;  
6. Start LAMP:  
```sudo /etc/init.d/apache2 start```  
7. Move to server/index.php folder and start php server:  
```cd /var/www/html/sensor-main/vendor/morozovsk/websocket-examples/chat/server/```  
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
For example http://192.168.1.47/sensor-main/vendor/morozovsk/websocket-examples/chat/client/


***
> **Resources**:
>> **https://github.com/morozovsk/websocket** - PHP Websocket Class.  
>> **https://github.com/Xowap/PHP-Serial** - PHP Serial Class.  
>> **https://github.com/hongkiat/svg-meter-gauge** - Simple SVG-meter.  
>> **https://github.com/meetanthony/crcphp** - Calculating CRC32 Class.  

  
