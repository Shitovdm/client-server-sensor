<?php
/**
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
        //echo 'true';
        $Text = "Синхронизация с сервером...";
        //$NextAction = "Проверка наличия обновлений...";
        $NextAction = "syncRepo";
    }
}

//  Проверка наличия обновлений.

//  Скачивание последних обновлений.
if($act === "syncRepo"){
    //$Text = shell_exec('sudo ./update.sh');
    //$Text = shell_exec("/var/www/html/service/update.sh 2>&1");
    exec("/var/www/html/service/copy_repo.sh");
    $Text = "Установка обновлений...";
    $NextAction = "moveFiles";
}

//  Позиционирование файлов по нужным папкам.
if($act === "moveFiles"){
    //$Text = shell_exec('sudo ./update.sh');
    //$Text = shell_exec("/var/www/html/service/update.sh 2>&1");
    exec("/var/www/html/service/move_files.sh");
    $Text = "Завершение установки...";
    $NextAction = 0;
}


//$Text = "Синхронизация с сервером...";

$response = array(
    "text" => $Text,
    "nextAction" => $NextAction
);

echo(json_encode($response));

?>