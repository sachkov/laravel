var vm = new Vue({
    el: '#appV',
    data: {
        mainTable: [],
        add_users_table: [],
        edit: {},           //Объект - редактируемая в данный момент нужда
        done: {},           //Завершаемая в данный момент нужда
        arUsers: [],
        edit_users_table: [],
        is_thanks: 0,           //Является ли нужда - благодарностью
        groups_table: [],       //Основной массив с группами
        add_groups_table: [],   //Список групп при добавлении мн
        edit_groups_table: [],  //Список групп при редактировании мн
    },
    methods:{
        del: function(a){
            vm.add_users_table.splice(a,1);
        },
        edit_del: function(a){
            vm.edit_users_table.splice(a,1);
        },
        toggle_description(indx){
            var temp = vm.mainTable[indx];
            temp.description_show = !temp.description_show;
            vm.$set(vm.mainTable, indx, temp);
        },
        show_edit_form(indx, event){
            if($(event.target).hasClass("active")){
                closeEditForm();
            }else{
                closeDoneForm();//на всякий случай прячем форму завершения
                
                $(event.target).addClass("active");
                $("#container-"+indx).append($(".edit-form"));
                $(".edit-form").show();
                vm.edit = vm.mainTable[indx];
                vm.edit_users_table = vm.mainTable[indx].users;
                vm.edit_groups_table = vm.mainTable[indx].groups;
            }
        },
        show_done_form(indx, event){
            if($(event.target).hasClass("active")){
                closeDoneForm();
            }else{
                //Если опубликована благодарность то ее можно только закрыть
                if(vm.mainTable[indx].is_thanks){
                    saveDoneForm(vm.mainTable[indx].id);
                    return true;
                }
                closeEditForm();//на всякий случай прячем форму редактирования
                
                $(event.target).addClass("active");
                $("#container-"+indx).append($(".done-form"));
                $(".done-form").show();
                vm.done = vm.mainTable[indx];
            }
        },
        del_gr: function(a){
            vm.add_groups_table.splice(a,1);
        },
        edit_del_gr: function(a){
            vm.edit_groups_table.splice(a,1);
        },
    },
});

var arUsers = {}; //Список всех пользователей
//var mainTable;      //Основная таблица МН
//var MNeditID;       //ID элемента, который в данный момнет редактируется

$( document ).ready(function(){
    
    if(location.pathname && location.pathname.indexOf('/home')+1){
        if(auth){

            getTable();//Получение основной таблицы молитвенных нужд
            
            getUsers();//Получение списка пользователей для "поделится"

            getGroups();//Получение списка групп для "поделится"
        }
    }

    //Нажатие на "Добавить"
    $("#btn-add-mn").on("click",function(){
        $(this).parent().hide();
        $("#create-form").show();
    });
    //Нажатие на "Сохранить" (Добавление МН)
    $("#btn-save-mn").on("click",function(event){
        //отправка ajax
        saveMN();
    });
    //Нажатие на Отменить (Добавление МН)
    $("#btn-cancel-mn").on("click",function(event){
        $("#create-form").hide();
        $("#btn-add-mn").parent().show();
        $("#input-name").val("");
        $("#textarea-descr").val("");
        $("#input-user").val("");
        vm.add_users_table = [];
        vm.add_groups_table = [];
    });
    
    //Нажатие на "Сохранить" (Редактирование МН)
    $("#btn-save-edit").on("click",function(event){
        //отправка ajax
        editMN();
    });
    
    //Нажатие на "Отменить" (Редактирование МН)
    $("#btn-cancel-edit").on("click",function(event){
        closeEditForm();
    });
    
    //Нажатие на "Показать описание"
    $(".mn-show").on("click",function(){
        if($(this).hasClass("active")){
            $(this).removeClass("active");
            $(this).find("span").html("Показать описание");
            $("#desc-"+$(this).parents(".t-tr").data("mnid")).hide();
        }else{
            $(this).addClass("active");
            $(this).find("span").html("Скрыть описание");
            $("#desc-"+$(this).parents(".t-tr").data("mnid")).show();
        }
    });
});

function getTable(offset = 0){
    $.ajax({
        type: "POST",
        url: "/ajax/getTable",
        dataType: "json",
        headers: {
                'X-CSRF-TOKEN': $('#x_token').val()
            },
        data: {
            offset: offset
        },
        success: function(data){
            if(!data.table && !data.count){
                console.log("getting error");
                return false;
            }
            //mainTable = data.table;
            if(offset){
                for(x in data.table)
                    vm.mainTable.push(data.table[x]);
            }else vm.mainTable = data.table;
                
            if(data.count && vm.mainTable.length < data.count){
                $("#more_btn").show();
            }else{
                $("#more_btn").hide();
            }
        },
        error: function(data) {
            console.log("error");
            console.log(data);
        }
    });
}

function getUsers(){
    $.ajax({
        type: "POST",
        url: "/ajax/getUsers",
        dataType: "json",
        headers: {
                'X-CSRF-TOKEN': $('#x_token').val()
            },
        success: function(data){
            var arUsers = [];
            for(x in data){
                arUsers.push({label: data[x], value:x});
            }
            $("#input-user").autocomplete({
                minLength: 1,
                source: arUsers,
                select: function( event, ui ) {
                    vm.add_users_table.push(ui.item);
                },
                close: function( event, ui ){
                    $("#input-user").val("");
                }
            });
            $("#share-edit").autocomplete({
                minLength: 1,
                source: arUsers,
                select: function( event, ui ) {
                    vm.edit_users_table.push({name: ui.item.label, id: parseInt(ui.item.value,10)});
                },
                close: function( event, ui ){
                    $("#share-edit").val("");
                }
            });
        },
        error: function(data) {
            console.log("error");
            console.log(data);
        }
    });
}

function saveMN(){
    // Находим массив ID пользователи, с которыми поделились молитвой
    var name = $("#input-name").val();
    if(!name || name.length < 3){
        $("#input-name").addClass("is-invalid");
        $("#input-name").one("change", function(){
            $("#input-name").removeClass("is-invalid");
        });
        return false;
    }
    let res = [];
    let resG = [];
    for(i in vm.add_users_table){
        res.push(vm.add_users_table[i].value);
    }
    for(i in vm.add_groups_table){
        resG.push(vm.add_groups_table[i].value);
    }
    //отправка ajax
    $.ajax({
        type: "POST",
        url: "/ajax/saveMN",
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('#x_token').val()
        },
        data: {
            _token: $('#x_token').val(),
            name: name,
            text: $("#textarea-descr").val(),
            users: JSON.stringify(res),
            groups: JSON.stringify(resG),
        },
        success: function(data){
            $("#create-form").hide();
            $("#btn-add-mn").parent().show();
            getTable();
        },
        error: function() {
            console.log("error");
        }
    });
}
//Закрытие формы редактирования
function closeEditForm(){
    $("#name-edit").val("");
    $("#descr-edit").val("");
    $("#result-edit").val("");
    vm.edit_users_table = [];
    vm.edit_groups_table = [];
    $(".mn-act.active").removeClass("active");
    $(".edit-form").hide();
}
//Закрытие формы завершения
function closeDoneForm(){
    vm.edit_users_table = [];
    vm.edit_groups_table = [];
    $(".mn-act.active").removeClass("active");
    $(".done-form").hide();
}
/*
 * Завершение публикации молитвы с возможностью добавить результат
 * и опубликовать как благодарность
 * param done - завершить молитву без указания результата и переопубликования.
 */
function saveDoneForm(id = 0){
    var result;
    var re_publish;
    var mn_id;
    if(id){
        mn_id = id;
        result = "";
        re_publish = 0;
    }else{
        mn_id = vm.done.id;
        result = vm.done.answer;
        re_publish = vm.done.is_thanks?1:0;
    }
    if(re_publish == 1 && !result){
        $("#result-done").addClass("is-invalid");
        $("#result-done").one("change", function(){
            $("#result-done").removeClass("is-invalid");
        });
        return false;
    }
    $.ajax({
        type: "POST",
        url: "/ajax/doneMN",
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('#x_token').val()
        },
        data: {
            _token: $('#x_token').val(),
            //id: MNeditID,
            id: mn_id,
            result: result,
            re_publish: re_publish
        },
        success: function(data){
            //Изменение значений в таблице и закрытие формы редактирования
            closeDoneForm();
            if(!re_publish) $(".t-tr[data-mnid="+mn_id+"]").hide();
        },
        error: function(data) {
            console.log("doneMN error");
            console.log(data);
        }
    });
}

function editMN(){
    // Находим массив ID пользователи, с которыми поделились молитвой
    let resU = [];
    let resG = [];
    //var name = $("#name-edit").val();
    var name = vm.edit.name;
    if(!name || name.length < 3){
        $("#name-edit").addClass("is-invalid");
        $("#name-edit").one("change", function(){
            $("#name-edit").removeClass("is-invalid");
        });
        return false;
    }
    //var description = $("#descr-edit").val();
    for(i in vm.edit_users_table){
        resU.push(vm.edit_users_table[i].id);
    }
    for(i in vm.edit_groups_table){
        resG.push(vm.edit_groups_table[i].id);
    }
    //отправка ajax
    $.ajax({
        type: "POST",
        url: "/ajax/editMN",
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('#x_token').val()
        },
        data: {
            _token: $('#x_token').val(),
            //id: MNeditID,
            id: vm.edit.id,
            name: name,
            //text: description,
            text: vm.edit.description,
            result: vm.edit.answer,
            users: JSON.stringify(resU),
            groups: JSON.stringify(resG),
        },
        success: function(data){
            //Изменение значений в таблице и закрытие формы редактирования
            /*$(".t-tr[data-mnid='"+MNeditID+"'] .t-name").html(name);
            $("#desc-"+MNeditID+" td").html(description);*/
            closeEditForm();
        },
        error: function() {
            console.log("editMN error");
        }
    });
}

/*
 * Загрузка дополнительных нужд
 */
function getMorePrayers(){
    var offset = vm.mainTable.length;
    getTable(offset);
}

///////Функционал групп ////////
/*
    Получение всех групп
*/
function getGroups(){
    $.ajax({
        type: "POST",
        url: "/personal/getGroups",
        dataType: "json",
        data: {
            _token: $('#x_token').val()
        },
        success: function(data){
            try{
                groups_table = data.groups;
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
*   Наполнение автокомплита для поиска существующих групп
*/
function fillAutocomplite(groups){
    let table = [];
    for(x in groups){
        if(groups[x].is_member){
            table.push({
                'value': groups[x].id,
                'label':groups[x].name
            });
        }
    }
    $('#input-group').autocomplete({
        minLength: 1,
        source: table,
        select: function( event, ui ) {
            vm.add_groups_table.push(ui.item);
            return false;
        },
        change: function( event, ui ) {
            $("#input-group").val("");
        }
    });
    $('#groups-edit').autocomplete({
        minLength: 1,
        source: table,
        select: function( event, ui ) {
            vm.edit_groups_table.push({
                name: ui.item.label, 
                id: parseInt(ui.item.value,10)
            });
            return false;
        },
        change: function( event, ui ) {
            $("#groups-edit").val("");
        }
    });
}