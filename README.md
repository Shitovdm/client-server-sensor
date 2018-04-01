# client-server-sensor

<h3>Server Start</h3>

1. Use ubuntu-16.04.4-desktop-i386;
2. Install LAMP:
    1. sudo apt-get install lamp-server^ phpmyadmin
    2. sudo /etc/init.d/apache2 start
    3. sudo chmod -R 777 /var/www
3. Install FTP-server:
    1. sudo apt-get install vsftpd
    2. sudo systemctl start vsftpd
    3. sudo systemctl enable vsftpd
    4. sudo ufw allow 20/tcp
    5. sudo ufw allow 21/tcp
    6. sudo cp /etc/vsftpd.conf /etc/vsftpd.conf.orig
    7. sudo nano /etc/vsftpd.conf
    8. Edit:
        1. anonymous_enable = NO
        2. local_enable = YES
        3. write_enable = YES
        4. local_umask = 022
        5. dirmessage_enable = YES
        6. xferlog_enable = YES
        7. xferlog_std_format=YES
        8. connect_from_port_20 = YES
        9. listen=YES
        10.  listen_ipv6=NO
        11.  pam_service_name=vsftpd
        12.  userlist_enable = YES
        13.  userlist_file=/etc/vsftpd.userlist
        14.  userlist_deny=NO
    9. Create new user:
        1  sudo useradd -m -c "Test User" -s /bin/bash testuser
        2.  sudo passwd testuser
            1.  echo "testuser" | sudo tee -a /etc/vsftpd.userlist
            2.  cat /etc/vsftpd.userlist
4. Restart FTP-server
5. Paste all project files into /var/www/html
6. Start LAMP:
    1. sudo /etc/init.d/apache2 start
7. Move to server/index.php folder and start php server:
    1. cd /var/www/html/sensor-main/vendor/morozovsk/websocket-examples/chat/server/
    2. php index.php start

> Links
>> http://linux-user.ru/distributivy-linux/programmy-dlya-linux/lokalnyj-server-lamp-dlya-ubuntu-linux-mint/
>> http://rus-linux.net/MyLDP/server/ftp.html
>> https://losst.ru/ustanovka-ftp-na-ubuntu-16-04


<h3>Client</h3>

1. Go to local server address
For example http://192.168.1.47/sensor-main/vendor/morozovsk/websocket-examples/chat/client/
  
