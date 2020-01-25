
$( document ).ready(function(){
    //генерировать код приглашения
    $("#generate").on("click", function(){
        generate();
        $(this).hide();
    });
    //создать новую группу
    $("#create-group").on("click", function(){
        if($("#group-name").val() != "")
            createGroup();
    });
    // присоединится к группе
    $("#come-in-group").on("click", function(){
        if($(this).hasClass("act")){
            addUser();
        }
    });
    getGroups();
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
////////Покидание группы, удаление и переименование//////////
function leave(group_id){
    globalAjax(
        "/personal/leaveGroup",
        {group: group_id},
        function(data){
            location.reload();
        },
        ()=>{}
    );
}

///////Получение всех групп//////
let selected_group_id = 0;  //ID группы в которую хочет вступить
/*
 * Получение всех групп
 */
function getGroups(){
    $.ajax({
        type: "POST",
        url: "/personal/getGroups",
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('#x_token').val()
        },
        data: {
            _token: $('#x_token').val()
        },
        success: function(data){
            try{
                console.table(data.groups);
                fillTable(data.groups);
                fillAutocomplite(data.groups);
            }catch{
                console.log("getGroups data error!");
            }
        },
        error: function(data) {
            console.log("getGroups error");
            console.log(data);
        }
    });
}
/*
*   Присоединить пользователя к существующей группе
*/
function addUser(){

    if(!selected_group_id) return false;

    globalAjax(
        "/personal/addUser",
        {group: selected_group_id},
        function(data){
            console.log(data);
            location.reload();
        },
        function(){}
    );
}
/* 
* Наполнение таблицы моих групп  
*/
function fillTable(groups){
    let html = "";
    for(x in groups){
        if(groups[x].is_member){
            $('.table_groups').show();
            html += '<tr><td>';
            if(groups[x].is_author){
                html += '<input class="group_edit" value="';
            }
            html += groups[x].name+'('+groups[x].number+')';
            if(groups[x].is_author){
                html += '">';
            }
            html += '</td><td><button class="personal-btn"';
            html += 'onclick="leave('+groups[x].id+')">';
            html += 'Покинуть группу</button>';
            if(groups[x].is_author){
                html += '<button class="personal-btn"';
                html += 'onclick="del_group('+groups[x].id+')">';
                html += 'Удалить группу</button>';
            }
            html += '</td></tr>';
        }
    }
    $('.table_groups table').html(html);
}

/*
*   Наполнение автокомплита для поиска существующих групп
*/
function fillAutocomplite(groups){
    let data = [];
    for(x in groups){
        if(!groups[x].is_member){
            data.push({
                'value': groups[x].id,
                'label':groups[x].name+'('+groups[x].number+')'
            });
        }
    }
    $('#select-group').autocomplete({
        minLength: 1,
        source: data,
        select: function( event, ui ) {
            console.log(ui.item);
            selected_group_id = ui.item.value;
            $('#select-group').val(ui.item.label);
            $("#come-in-group").addClass("act");
            return false;
        },
        change: function( event, ui ) {
            if(ui.item == null){
                $("#come-in-group").removeClass("act");
                selected_group_id = 0;
            }
        }
    });
}

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