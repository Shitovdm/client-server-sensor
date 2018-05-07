/* set radius for all circles */
var r = 200;
var limit = 30;     //  +-30 град/мин.
var data_frequency = 100;
var sensitivity_arrows = 50;
var dark_theme = false;

//  Изменение предела измерений из главного окна, просто +-15 при каждом нажатии.
function setLimit(act){
    if(limit === 300 && act === 1){
    }else{
        if(limit === 30 && act === 0){ 
        }else{
            if(act === 1){
                //  Увеличиваем значение предела.
                limit += 15;
            }else{
                //  Уменьшаем значение предела.
                limit -= 15;
            }
        }
    }
    
    $("#setLimit-value").text(limit);
    
    //  Изменяем значения на шкале.
    $("#division_3_3").text(limit);
    $("#division_2_3").text((limit/3)*2);
    $("#division_1_3").text(limit/3);
    $("#division_M3_3").text(-limit);
    $("#division_M2_3").text(-(limit/3)*2);
    $("#division_M1_3").text(-limit/3);
    $('#measuarement_limit').val(limit);
}

//  Регулировка чувствительности стрелки.
$('#sensitivity-arrows').on("change",function(){
     //sensitivity_arrows = this.value;
     $("#meter_needle").css("transition-duration", (1 - (this.value / 100)) + "s"); 
}); 

//  Регулировка уровня помех.
$('#interference-correction').on("change",function(){
    console.log(this.value);
}); 

//  Частота обновления цифровых значений.
$('#filtering-digital').on("change",function(){
    console.log(this.value);
}); 

//  Частота выдачи данных.
$('#data_frequency').on("change",function(){
    data_frequency = this.value;
}); 

//  Предел измерений.
$('#measuarement_limit').on("change",function(){
    limit = Number(this.value);
    $("#setLimit-value").text(limit);
    //  Изменяем значения на шкале.
    $("#division_3_3").text(limit);
    $("#division_2_3").text((limit/3)*2);
    $("#division_1_3").text(limit/3);
    $("#division_M3_3").text(-limit);
    $("#division_M2_3").text(-(limit/3)*2);
    $("#division_M1_3").text(-limit/3);
}); 

//  Сброс настроек к заводским.
function resetParams(){
    console.log("Все параметры выставлены по умолчанию.");
    $("#sensitivity-arrows").val(50);
    $("#interference-correction").val(20);
    $("#filtering-digital").val(50);
    $("#data_frequency").val(100);
}

//  Стили при уменьшении окна настроек.
$("#minimize-settings").on("click",function(){
    $("#minimize-settings").css("display","none");
    $(".settings .settings-param:nth-child(6)").css("display","none");
    $(".settings .settings-param:nth-child(7)").css("display","none");
    $(".settings").css({
        "height":"393px",
        "top":"-141px",
        "border-radius":"0px 0px 10px 10px"
    });
    $(".settings-control").css("display","none");
    $(".maximize-button").css("display","block");
});

//  Изменяем стрила при установке исходного изображения окна настроек.
$("#maximize-settings").on("click",function(){
    $(".maximize-button").css("display","none");
    $(".settings").css({
        "height":"600px",
        "top":"-601px",
        "border-radius":"10px"
    });
    //  Задержка перед появлением кнопок.
    setTimeout(function(){
        $("#minimize-settings").css("display","block");
        $(".settings .settings-param:nth-child(6)").css("display","block");
        $(".settings .settings-param:nth-child(7)").css("display","block");
        $(".settings-control").css("display","block");
    },450); 
});

//  Изменение цветов темной темы.
$('#dark-theme').on("change",function(){
    if(dark_theme === false){
        dark_theme = true;
        //  Стили для темной темы.
        $(".body").css("background-color","#303030");
        $(".settings").css("background-color","#424242;!important");
        $(".settings-param-title").css("color","#d8d8d8");
        $(".range-slider::-webkit-slider-runnable-track").css("background-color","#6f6f6f");

        $(".range-slider::-webkit-slider-thumb").css("background-color","#b9b9b9!important");
        $(".range-slider::-webkit-slider-thumb:hover").css("background-color","#b1b1b1!important");

        //  Кнопки.
        $(".update-button").css("background-color","#37474F!important");
        $(".update-button:hover").css("background-color","#888888!important");

        $("#save-button").css("background-color","#263238!important");
        $("#default-button").css("background-color","#37474F!important");
        $("#exit-button").css("background-color","#6DA7A2!important");
        $(".settings-control-action").css("color","#d8d8d8");

        //  Чекбоксы.
        $("input[type='checkbox'] + label::before").css("background-color","#6f6f6f");
        $("input[type='checkbox'] + label::after").css("background-color","#b9b9b9");
        $("input[type='checkbox'] + label::before").css("border","1px solid #6f6f6f");
        $("input[type='checkbox'] + label::after").css("border","1px solid #6f6f6f");

        //  Основная страница
        $("#meter").css("border-radius","10px");
        $(".sevenSeg-svg").css("background-color","#424242!important");
        $(".sevenSeg-svg").css("fill","#808080!important");

        $(".rivet").css("box-shadow","5px 3px 20px rgba(62,62,62,0.3), -5px -10px 20px rgba(0,0,0,0.5)");
        $(".rivet").css("background","radial-gradient(farthest-side ellipse at top left, #808080, #aaaaaa)");
        $(".control-panel").css("border","1px dashed #424242");

        $("#toUp-limit").attr("src", "client/img/icons/toUp_white.png");
        $("#toDown-limit").attr("src", "client/img/icons/toDown_white.png");
        $("#setings-icon").attr("src", "client/img/icons/settings_white.png");
        $("#save-stat-icon").attr("src", "client/img/icons/saveAs_white.png");

        $(".trigger-control").css("border", "1px solid #808080");
        $("#meter").css("background-color","#424242!important");
        $(".sevenSeg-svg").css({fill:"#505050"});
    }else{
        dark_theme = false;
        //  Стили для светлой темы.
        $(".body").css("background-color","#ffffff");
        $(".settings").css("background-color","#efefef;!important");
        $(".settings-param-title").css("color","#333");
        $(".range-slider::-webkit-slider-runnable-track").css("background-color","#F6F6F6");
        
        $(".range-slider::-webkit-slider-thumb").css("background-color","#fff!important");
        $(".range-slider::-webkit-slider-thumb:hover").css("background-color","#F6F6F6!important");

        //  Кнопки.
        $(".update-button").css("background-color","#6bbf6e!important");
        $(".update-button:hover").css("background-color","#4CAF50!important");
        
        $("#save-button").css("background-color","#6bbf6e!important");
        $("#default-button").css("background-color","#ccc!important");
        $("#exit-button").css("background-color","#E04644!important");
        $(".settings-control-action").css("color","#f9f9f9");
        
        //  Чекбоксы.
        $("input[type='checkbox'] + label::before").css("background-color","#dddddd");
        $("input[type='checkbox'] + label::after").css("background-color","#fff");
        $("input[type='checkbox'] + label::before").css("border","1px solid #dadada");
        $("input[type='checkbox'] + label::after").css("border","1px solid #dadada");
        
        //  Основная страница
        $("#meter").css("border-radius","10px");
        $(".sevenSeg-svg").css("background-color","#efefef!important");
        $(".sevenSeg-svg").css("fill","#e1e1e1!important");

        $(".rivet").css("box-shadow","5px 3px 20px rgba(62,62,62,0.3), -5px -10px 20px rgba(255,255,255,0.5)");
        $(".rivet").css("background","radial-gradient(farthest-side ellipse at top left, white, #aaaaaa)");
        $(".control-panel").css("border","1px dashed #ccc");
        
        $("#toUp-limit").attr("src", "client/img/icons/toUp.png");
        $("#toDown-limit").attr("src", "client/img/icons/toDown.png");
        $("#setings-icon").attr("src", "client/img/icons/settings.png");
        $("#save-stat-icon").attr("src", "client/img/icons/saveAs.png");

        $(".trigger-control").css("border", "1px solid #ccc");
        $("#meter").css("background-color","#efefef!important");
        $(".sevenSeg-svg").css({fill:"#e1e1e1"});
    }
}); 

// Убираем окно настроек.
$("#exit-button").click(function() {
  $("#settings").fadeOut("fast", function() {});
  $(".maximize-button").css("display","none");
  $(".minimize-button").css("display","none");
});

//  Показываем окно с настройками.
$("#show-settings").click(function() {
  $("#settings").fadeIn("fast", function() {});
  $(".maximize-button").css("display","none");
  $(".minimize-button").css("display","block");
  
});

//  Отмена установки обновлений.
$("#confirm-no").click(function() {
    $(".update-window").css("display","none"); 
    $(".background-curtain").css("display","none");
});

//  Подтверждение установки обновлений.
$("#confirm-yes").click(function() {
    $(".confirm-update").css("display","none"); 
    $(".uptade-preloader").css("display","block");
    $("#update-status").html("Выполняется проверка соединения с интернетом...");
    //  Ajax запрос к серверу для скачивания обновлений.
    ajaxRequert("checkConnection");
});

//  Подтверждение выхода из окна настроек.
$("#confirm-ok").click(function() {
    $(".update-window").css("display","none"); 
    $(".background-curtain").css("display","none");
    $(".update-final").css("display","none");  //  Скрываем кнопку "ОК";
});

/**
 * Рекурсивное выполнение запросов к серверному скрипту. Один запрос, одно действие.
 * @param {type} action
 * @returns {undefined}
 */
function ajaxRequert(action){
    $.ajax({
        url: 'upgrade/update.php', 
        type:'POST',
        dataType: 'json',
        data: {act : action},
        success: function(response){
            console.log(response);
            $("#update-status").html(response.text);
            if(response.nextAction !== 0){
                $(".uptade-preloader").css("display","block");  //  Показываем прелоадер.
                $(".update-final").css("display","none");  //  Скрываем кнопку "ОК";
                ajaxRequert(response.nextAction);
            }else{
                console.log("Конец выполнения запросов.");
                $(".update-final").css("display","block");  //  Показываем кнопку "ОК";
                $(".uptade-preloader").css("display","none");  //  Скрываем прелоадер.
            }
        }
    });
}

//  Функция сохранения пользовательских параметров.
//  Делает запрос к серверу, который в свою очередь создает файл с пользовательскими настройками и сохраняет его.
function saveUserParams(){
    var sensitivity_arrows = $("#sensitivity-arrows").val();
    var interference_correction = $("#interference-correction").val();
    var filtering_digital = $("#filtering-digital").val();
    var data_frequency = $("#data_frequency").val();
    var measuarement_limit = $("#measuarement_limit").val();
    
    console.log(sensitivity_arrows, interference_correction, filtering_digital, data_frequency, measuarement_limit);
    
}

//  При нажатии на кнопку "обновить".
function cloudUpdateConfirmation(){
    $(".update-window").css("display","block");
    $(".background-curtain").css("display","block");
    $("#update-status").html("Вы действительно хотите начать установку обновлений?");
    $(".confirm-update").css("display","block");
}


var circles = document.querySelectorAll('.circle');
var total_circles = circles.length;
for (var i = 0; i < total_circles; i++) {
    circles[i].setAttribute('r', r);
}
/* set meter's wrapper dimension */
var meter_dimension = (r * 2) + 100;
var wrapper = document.querySelector('#wrapper');
wrapper.style.width = meter_dimension + 100 + 'px';
wrapper.style.height = meter_dimension + 100 + 'px';
/* add strokes to circles  */
var cf = 2 * Math.PI * r;
var semi_cf = cf / 2;
var semi_cf_1by6 = semi_cf / 6;         //  low-2
var semi_cf_2by6 = semi_cf_1by6 * 2;    //  avg-2
var semi_cf_3by6 = semi_cf_1by6 * 3;    //  high-2
var semi_cf_4by6 = semi_cf_1by6 * 4;    //  high
var semi_cf_5by6 = semi_cf_1by6 * 5;    //  avg

document.querySelector('#outline_curves').setAttribute('stroke-dasharray', semi_cf + ',' + cf);
document.querySelector('#low').setAttribute('stroke-dasharray', semi_cf + ',' + cf);
document.querySelector('#avg').setAttribute('stroke-dasharray', semi_cf_5by6 + ',' + cf);
document.querySelector('#high').setAttribute('stroke-dasharray', semi_cf_4by6 + ',' + cf);
document.querySelector('#high-2').setAttribute('stroke-dasharray', semi_cf_3by6 + ',' + cf);
document.querySelector('#avg-2').setAttribute('stroke-dasharray', semi_cf_2by6 + ',' + cf);
document.querySelector('#low-2').setAttribute('stroke-dasharray', semi_cf_1by6 + ',' + cf);
document.querySelector('#outline_ends').setAttribute('stroke-dasharray', 2 + ',' + (semi_cf - 2));

/*bind range slider event*/
var slider = document.querySelector('#slider');
var lbl = document.querySelector("#lbl");
var mask = document.querySelector('#mask');
var meter_needle = document.querySelector('#meter_needle');

function range_change_event(value) {
    var percent = value;
    var digit = 0;
    //var percent = 20;
    var angle = 0;
    //  Граничные значения поворота стрелки.
    if(percent > limit){  //  Крайнее правое положение.
        angle = 90.0;
        digit = limit;
    }else{
        if(percent < -limit){ //  Кранее левое положение.
            angle = -90.0;
            digit = -limit;
        }else{
            angle = (percent * 90) / limit;
            digit = angle;
        }
    }
    //  Поворачиваем стрелку.
    meter_needle.style.transform = 'rotate(' + angle + 'deg)';
    //console.log(value);
    //  Изменяем семисегментное значение.
    $("#exampleArray").sevenSeg({ value: digit.toFixed(1) }); 
}

var value = 0;  //  Значение.

 $("#exampleArray").sevenSeg({ digits: 4, value: 0 });       
 
 // Определение функций вебсокетного соединения.
 $(function(){
    $("#exampleArray").sevenSeg({ value: "----" }); 
    function wsStart() {
        //ws = new WebSocket("ws://192.168.1.40:8002/");
        ws = new WebSocket("ws://10.3.141.1:8002/");
        ws.onopen = function() { 
            console.log("Соединение успешно открыто.");
            console.log("Начало передачи данных.");
        };
        ws.onclose = function() { 
            console.log("Соединение закрыто, пытаюсь переподключиться...");
            $("#exampleArray").sevenSeg({ value: "----" }); 
            meter_needle.style.transform = 'rotate(0deg)';
            setTimeout(wsStart, 1000);
        };
        ws.onmessage = function(evt) { 
           //console.log(evt.data );
            if(evt.data !== undefined && evt.data !== null && evt.data !== "COM-порт на сервере успешно открыт."){
                var value = (evt.data * 60).toFixed(1); // град/мин.
                range_change_event(value);
                setTimeout(function(){
                    ws.send("1");
                }, (500 - (data_frequency*5)) );
                
            }
        };
    }
    wsStart();
});
