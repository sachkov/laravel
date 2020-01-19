
$( document ).ready(function(){
    $("#generate").on("click", function(){
        generate();
        $(this).hide();
    });
});
/*
 * Запрос на получение код для приглашения нового пользователя
 */
function generate(){

    //отправка ajax
    $.ajax({
        type: "POST",
        url: "/personal/generate",
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('#x_token').val()
        },
        data: {
            _token: $('#x_token').val(),
        },
        success: function(data){
            try{
                $("#new_invite").show();
                $("#invite_code").html(data);
            }catch{
                console.log("generate data error!");
            }
        },
        error: function(data) {
            console.log("generate error");
            console.log(data);
        }
    });
}
