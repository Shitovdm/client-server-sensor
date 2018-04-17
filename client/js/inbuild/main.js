

//  Диапазоны измерения.
var limit = 300;     //  +-30 град/мин.
$("#division_3_3").text(limit);
$("#division_2_3").text((limit/3)*2);
$("#division_1_3").text(limit/3);

$("#division_M3_3").text(-limit);
$("#division_M2_3").text(-(limit/3)*2);
$("#division_M1_3").text(-limit/3);

/* set radius for all circles */
var r = 200;


var circles = document.querySelectorAll('.circle');
var total_circles = circles.length;
for (var i = 0; i < total_circles; i++) {
    circles[i].setAttribute('r', r);
}
/* set meter's wrapper dimension */
var meter_dimension = (r * 2) + 100;
var wrapper = document.querySelector('#wrapper');
wrapper.style.width = meter_dimension + 100 + 'px';
wrapper.style.height = meter_dimension +100 + 'px';
/* add strokes to circles  */
var cf = 2 * Math.PI * r;
var semi_cf = cf / 2;
var semi_cf_1by6 = semi_cf / 6; //  low-2
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
    console.log(percent);
    //var percent = 20;
    var angle = 0;
    //  Граничные значения поворота стрелки.
    if(percent > limit){  //  Крайнее правое положение.
        angle = 90;
    }else{
        if(percent < -limit){ //  Кранее левое положение.
            angle = -90;
        }else{
            angle = (percent * 90) / limit;
        }
    }
    //  Поворачиваем стрелку.
    meter_needle.style.transform = 'rotate(' + angle + 'deg)';
    //  Изменяем семисегментное значение.
    $("#exampleArray").sevenSeg({ value: percent }); 
}

var value = 0;  //  Значение.

 $("#exampleArray").sevenSeg({ digits: 4, value: 0 });       
 
 $(function(){
    function wsStart() {
        //ws = new WebSocket("ws://10.42.0.1:8002/");
        ws = new WebSocket("ws://192.168.1.47:8002/");
        ws.onopen = function() { 
           // $("#chat").append("<p>Клиент: соединение открыто</p>");
            console.log("Клиент: соединение открыто.");
        };
        ws.onclose = function() { 
            console.log("Клиент: соединение закрыто, пытаюсь переподключиться.");
            //$("#chat").append("<p>Клиент: соединение закрыто, пытаюсь переподключиться</p>"); 
            setTimeout(wsStart, 1000);
        };
        ws.onmessage = function(evt) { 
            if(evt.data !== undefined && evt.data !== null){
                var value = (evt.data * 60).toFixed(1); // град/мин.
                //console.log(value);
                //console.log(value);
                range_change_event(value);
                setTimeout(function(){
                    ws.send("1");   //  Продолжаем принимать данные.
                }, 20);
            }
        };
    }
    wsStart();
});