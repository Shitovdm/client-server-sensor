<?php

/**     @author Shitov Dmitry
 *      Скрипт производит поэтапный вызов bash скриптов для обновления системы.
 *      Необходимо максимально аккуратно изменять содержимое файла.
 *      Любые ошибки в этом файле приведут к краху не только системы обновлений но и к возможной неработоспособности всей системы.
 */

$act = $_POST["act"];   //  Выполняемое действие.

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
        $Text = "Загрузка обновлений...";
        $NextAction = "cloneRepo";
        sleep(2);
    }
}

//  Действие загрузки обновлений.
if($act === "cloneRepo"){
    //  Это работает, pull с перезаписью.
    exec("git fetch --all");
    exec("git reset --hard origin/master");
    exec("git pull origin master");
    $Text = "Изменение прав доступа к файлам и папкам...";
    $NextAction = "setPermission";
    sleep(3);
}

//  Изменение прав на все серверные файлы(не безопасно, но по другому не работает).
if($act === "setPermission"){
    //  Изменение прав доступа к файлу в директории upgrade.
    //exec("chmod +x README.md");
    //  Изменение прав доступа всего каталога upgrade.
    exec("chmod -R 777 .");
    $Text = "Перемещение новых файлов...";
    $NextAction = "moveFiles";
    sleep(1);
}

//  Операция перемещения файлов по каталогам.
if($act === "moveFiles"){
    //  Копирование файлов из каталога upgrade в корневой каталог сервера.(/var/www/html/)
    exec("cp html/index.html ../");
    exec("cp -r html/client ../");
    exec("cp -r html/vendor ../");
    
    //  Тут 2 пути: либо раскидывать файлы по папкам по назначению, а затем перемещать в соответствии со структурой сервера.
    //  Либо класть файлы сразу по каталогам как они будут лежать на сервере.
    //  Сейчас применен именно второй вариант, так как не требует перебора всех команд перемещения файлов.
    //exec("cp serial/gkv_udp_send ../vendor/morozovsk/websocket-examples/chat/server/");
    
    //  Эту стройчу трогать нельзя!
    exec("cp service/php/update.php ./");
    
    $Text = "Перемещение новых файлов...";
    $NextAction = 0;
    sleep(2);
}

$response = array(
    "text" => $Text,
    "nextAction" => $NextAction
);

echo(json_encode($response));

?>