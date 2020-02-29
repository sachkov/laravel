$( document ).ready(function(){

    //Нажатие на любое место на странице
    $('body').on("click", function(e){
        //..Закрывает меню
        if( $(".enter_link").hasClass("show") 
            && 
            !$(".mobile-menu").is(e.target)
        ){
            $(".enter_link").removeClass("show");
        }
    });

    // Нажатие на "открыть меню"
    $(".mobile-menu").on("click", function(){
        if($(".enter_link").hasClass("show"))
            $(".enter_link").removeClass("show");
        else
            $(".enter_link").addClass("show");
    });
});

/*
*   Хелпер для более короткого написания запроса
*/
function globalAjax(method, params, suc, er){
    params["_token"] = $('#x_token').val();
    $.ajax({
        type: "POST",
        url: method,
        dataType: "json",
        data: params,
        success: function(data){
            console.log("Success "+method);
            suc(data);
        },
        error: function(data) {
            console.log("Error "+method);
            console.log(data);
            er(data);
        }
    });
}