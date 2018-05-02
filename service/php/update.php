<?php

/**     @author Shitov Dmitry
 *      Скрипт производит поэтапный вызов bash скриптов для обновления системы.
 * 
 * 
 */

$act = $_POST["act"];

//  Проверка соединения с интернетом.
if($act === "checkConnection"){
    $x = fsockopen('yandex.ru', 80, $err, $ern, 1);
    if(!$x){
        //  Соединения нет.
        //echo 'false';
        $Text = "Нет соединения с интернетом. Установка обновлений невозможна.";
        $NextAction = 0;
    }else{ 
        //  Соединение есть.
        //  Переходим к загрузке обновлений.

        //  Это работает, pull с перезаписью.
        exec("git fetch --all");
        exec("git reset --hard origin/master");
        exec("git pull origin master");
        
        //  Изменение прав доступа к файлу в директории upgrade.
        //exec("chmod +x README.md");
        //  Изменение прав доступа всего каталога upgrade.
        exec("chmod -R 777 .");
        
        //  Копирование файлов из каталога upgrade в корневой каталог сервера.(/var/www/html/)
        exec("cp html/index.html ../");
        exec("cp -r html/client ../");
        exec("cp -r html/vendor ../");
        
        exec("cp serial/gkv_udp_send ../");
        
        exec("cp service/php/update.php ./");
        
        
        $Text = "Обновления успешно установлены.";
        $NextAction = 0;
        sleep(5);
    }
}

// Установка прав на исполнение bash скриптов.
/*if($act === "setPermissions"){
    exec("/etc/apache2/exec.sh");
    //exec("sudo ./permission.sh");
    
    sleep(2);
    $Text = $resp . "\n Синхронизация с сервером...";
    $NextAction = 0;
    //$NextAction = "syncRepo";
}*/

//  Скачивание последних обновлений.
/*if($act === "syncRepo"){
    //$Text = shell_exec('sudo ./update.sh');
    //$Text = shell_exec("/var/www/html/service/update.sh 2>&1");
    $res = exec("/var/www/html/service/copy_repo.sh");
    $Text = $res . " Установка обновлений...";
    $NextAction = "moveFiles";
    sleep(1);
}

//  Позиционирование файлов по нужным папкам.
if($act === "moveFiles"){
    //$Text = shell_exec('sudo ./update.sh');
    //$Text = shell_exec("/var/www/html/service/update.sh 2>&1");
    $res = exec("/var/www/html/service/ret/service/bash/move_files.sh");
    $Text = $res . " Установка обновлений окончена!";
    $NextAction = 0;
    sleep(1);
}*/

$response = array(
    "text" => $Text,
    "nextAction" => $NextAction
);

echo(json_encode($response));

?>