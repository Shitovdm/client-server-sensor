/* set radius for all circles */
var r = 200;
var limit = 30;     //  +-30 град/мин.

//  Диапазоны измерения.
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
    
    console.log("Set new limit: ",limit);
}



$(document).ready(function() {
    $(':checkbox').iphoneStyle();
});
$('#sensitivity-arrows').on("change",function(){
    console.log(this.value);
}); 

$('#default-button').on("click",function(){
    console.log("Все параметры выставлены по умолчанию.");
    $("#sensitivity-arrows").val(50);
    $("#interference-correction").val(20);
    $("#filtering-digital").val(50);
    $("#data_frequency").val(100);
    
}); 

$("#dark-theme").change(function() {
     if(this.checked) {
         console.log("213");
     }
 });

$("#dark-theme").parent("div").on("click",function() {
    console.log("we");
    if($("#dark-theme").checked) {
        console.log("ok");
    }
});


$("#exit-button").click(function() {
  $("#settings").fadeOut("fast", function() {});
  //$("#settings").css("display","none");
});

$("#show-settings").click(function() {
  $("#settings").fadeIn("fast", function() {});
  //$("#settings").css("display","block");
});

$("#confirm-no").click(function() {
    $(".update-window").css("display","none"); 
    $(".background-curtain").css("display","none");
});

$("#confirm-yes").click(function() {
    $(".confirm-update").css("display","none"); 
    $(".uptade-preloader").css("display","block");
    $("#update-status").html("Выполняется проверка соединения с интернетом...");
    //  Ajax запрос к серверу для скачивания обновлений.
    ajaxRequert("checkConnection");
});

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
    //var percent = 20;
    var angle = 0;
    //  Граничные значения поворота стрелки.
    if(percent > limit){  //  Крайнее правое положение.
        angle = 90.0;
    }else{
        if(percent < -limit){ //  Кранее левое положение.
            angle = -90.0;
        }else{
            angle = (percent * 90) / limit;
        }
    }
    //  Поворачиваем стрелку.
    meter_needle.style.transform = 'rotate(' + angle + 'deg)';
    //console.log(value);
    //  Изменяем семисегментное значение.
    $("#exampleArray").sevenSeg({ value: angle.toFixed(1) }); 
}

var value = 0;  //  Значение.

 $("#exampleArray").sevenSeg({ digits: 4, value: 0 });       
 
 $(function(){
    $("#exampleArray").sevenSeg({ value: "----" }); 
    function wsStart() {
        //ws = new WebSocket("ws://10.42.0.1:8002/");
        //ws = new WebSocket("ws://192.168.1.41:8002/");
        ws = new WebSocket("ws://192.168.42.1:8002/");
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
                ws.send("1");
            }
        };
    }
    wsStart();
});
