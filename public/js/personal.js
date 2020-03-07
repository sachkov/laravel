
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
    if($("#v-personal-groups").length){
        getGroups();
    }
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
                location.reload(true);
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
let vm = new Vue({
    el: '#v-personal-groups',
    data: {
        group_table: [],
        groups_available: [],   //Доступные для вступления группы
        selected_group: 0,
    },
    methods: {
        selectGroup: function(){
            if(vm.selected_group && vm.selected_group != "0"){
                $("#come-in-group").addClass("act");
            }else{
                $("#come-in-group").removeClass("act");
            }
        },
        addUser: function(){
            if($("#come-in-group").hasClass("act")){
                addUser();
            }
        },
    }
});

/* 
* Наполнение таблицы моих групп
Данные для таблицы получаем в запросе getGroups
*/
function fillTable(groups){
    for(x in groups){
        if(groups[x].is_member){
            vm.group_table.push(groups[x]);
        }else{
            vm.groups_available.push(groups[x]);
        }
    }
}

/////////// Получение всех групп ////////////

let selected_group_id = 0;  //ID группы в которую хочет вступить
let allGroups = [];         //Массив всех групп
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
                //console.table(data.groups);
                allGroups = data.groups;
                fillTable(data.groups);
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
    let id = $("#select-group").val();
    if(!id) return false;

    globalAjax(
        "/personal/addUser",
        {group: id},
        function(data){
            location.reload(true);
        },
        function(){}
    );
}

function leave(group_indx){
    globalAjax(
        "/personal/leaveGroup",
        {group: group_indx},
        function(){location.reload(true);},
        ()=>{}
    );
}

/*
*   Изменить название группы
*/
function changeName(group_indx){
    if($("#group_name").val() !=
        $(".info.group_name").html()
    ){ 
        globalAjax(
            "/personal/changeGroupName",
            {
                name: $("#group_name").val(),
                id: group_indx
            },
            function(){location.reload(true);},
            ()=>{location.reload(true);}
        );
    }
}

/*
*   Добавить пользователю статус админа
*/
function admin(group_id, user_id){
    globalAjax(
        "/personal/addAdmin",
        {
            group_id: group_id,
            user_id: user_id
        },
        function(data){
            console.log(data);
            location.reload(true);},
        ()=>{location.reload(true);}
    );
}

/*
*   Удалить статус админа для пользователя
*/
function del_admin(group_id, user_id){
    globalAjax(
        "/personal/delAdmin",
        {
            group_id: group_id,
            user_id: user_id
        },
        function(data){
            console.log(data);
            location.reload(true);},
        ()=>{location.reload(true);}
    );
}