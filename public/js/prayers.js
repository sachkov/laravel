//Основная таблица молитвенных нужд
$( document ).ready(function(){

    //Нажатие на "Добавить"
    $("#btn-add-mn").on("click",function(){
        $(this).hide();
        $("#create-form").show();
    });
    //Нажатие на "Сохранить"
    $("#btn-save-mn").on("click",function(event){
        //отправка ajax
        $("#create-form").hide();
        $("#btn-add-mn").show();
        $.ajax({
            type: "POST",
            url: "/ajax/saveMN",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('#x_token').val()
            },
            data: {
                _token: $('#x_token').val(),
                name: $("#input-name").val(),
                groups: $("#sel-groups").val(), 
                text: $("#textarea-descr").val(),
                users: $("#input-user").val(),
                date: $("#input-date").val()
            },
            success: function(data){
                console.log(data);
                console.log(data.date);
                console.log(data.name);
            },
            error: function() {
                console.log("error");
            }
        });
    });
    
});

