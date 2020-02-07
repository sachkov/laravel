
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
                location.reload();
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
        arGroupNames: [],  //массив имен групп для оприделения изменено ли значение
    },
    methods: {
        leave: function(group_indx){
            globalAjax(
                "/personal/leaveGroup",
                {group: vm.group_table[group_indx].id},
                function(data){
                    vm.group_table.splice(group_indx,1);
                    //location.reload();
                },
                ()=>{}
            );
        },
        del_group: function(group_indx){
            globalAjax(
                "/personal/delGroup",
                {group: vm.group_table[group_indx].id},
                function(data){
                    if(data.success)
                        vm.group_table.splice(group_indx,1);
                },
                ()=>{}
            );
        },
        saveName: function(group_indx){
            vm.arGroupNames[group_indx] = 
                vm.group_table[group_indx].name;
        },
        changeName: function(group_indx){
            if(vm.group_table[group_indx].name !=
                vm.arGroupNames[group_indx]
            ){
                globalAjax(
                    "/personal/changeGroupName",
                    {
                        name: vm.group_table[group_indx].name,
                        id: vm.group_table[group_indx].id
                    },
                    function(data){
                        console.log(data);
                    },
                    ()=>{}
                );
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
            //location.reload();
            for(x in allGroups)
                if(allGroups[x].id == selected_group_id)
                    break;
            if(data.error != undefined)
                vm.group_table.push(allGroups[x]);
            else console.log(data);
            $("#come-in-group").removeClass("act");
            $("#select-group").val("");
        },
        function(){}
    );
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
            //console.log(ui.item);
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