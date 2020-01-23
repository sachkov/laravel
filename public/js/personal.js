
$( document ).ready(function(){
    $("#generate").on("click", function(){
        generate();
        $(this).hide();
    });
    $("#create-group").on("click", function(){
        if($("#group-name").val() != "")
            createGroup();
    });
});
/*
 * Запрос на получение кода для приглашения нового пользователя
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

/*
 * Запрос на создание новой группы
 */
function createGroup(){

    $.ajax({
        type: "POST",
        url: "/personal/createGroup",
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('#x_token').val()
        },
        data: {
            _token: $('#x_token').val(),
            name: $("#group-name").val()
        },
        success: function(data){
            try{
                console.log(data);
            }catch{
                console.log("createGroup data error!");
            }
        },
        error: function(data) {
            console.log("createGroup error");
            console.log(data);
        }
    });
}
