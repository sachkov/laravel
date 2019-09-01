//Основная таблица молитвенных нужд
$( document ).ready(function(){

    //При нажатии на наименование, появляется/скрывается расширенное описание
    $(".js-click-Pname").on("click",function(){
        $(this).parent(".main").next().toggle(500);
    });
});

