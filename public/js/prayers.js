var vm = new Vue({
    el: '#appV',
    data: {
        mainTable: [],
        add_users_table: [],
        edit: {},           // Объект - редактируемая в данный момент нужда
        done: {},           // Завершаемая в данный момент нужда
        arUsers: [],
        edit_users_table: [],
        is_thanks: 0,           // Является ли нужда - благодарностью
        is_groups_table: false, // Состоит ли пользователь в группах
        add_groups_table: [],   // Список групп при добавлении мн
        //add_adm_groups_table: [], //Список админ групп при добавлении мн //l-7
        edit_groups_table: [],  // Список групп при редактировании мн
        active_index: 0,        // индекс элемента списка, на котором открыли меню
        mn_type: 0,             // тип добавляемой нужды (личная/от админа)

        user_groups: [],    //Группы, в которых состоит пользователь
        //admin_groups: [],   //Группы в которых автор - админ  //l-7
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
        show_edit_form(){
            if($(".edit-form").is(":visible")){
                closeEditForm();
            }else{
                closeDoneForm();//на всякий случай прячем форму завершения
                
                //$("#container-"+vm.active_index).append($(".edit-form"));
                $("#main-table").hide();
                $(".edit-form").show();
                vm.edit = vm.mainTable[vm.active_index];
                vm.edit_users_table = vm.mainTable[vm.active_index].users;
                vm.edit_groups_table = vm.mainTable[vm.active_index].groups;
            }
            vm.active_index = 0;
        },
        show_done_form(){
            if($(".done-form").is(":visible")){
                closeDoneForm();
            }else{
                //Если опубликована благодарность то ее можно только закрыть
                if(vm.mainTable[vm.active_index].is_thanks){
                    saveDoneForm(vm.mainTable[vm.active_index].id);
                    return true;
                }
                closeEditForm();//на всякий случай прячем форму редактирования
                
                //$("#container-"+vm.active_index).append($(".done-form"));
                $("#main-table").hide();
                $(".done-form").show();
                vm.done = vm.mainTable[vm.active_index];
            }
            vm.active_index = 0;
        },
        del_mn(){
            del_popap(vm.active_index);
            vm.active_index = 0;
        },
        drop(indx, e){
            $(e.target).parents(".list-item").append($(".drop-down-menu"));
            $(".drop-down-menu").show();
            vm.active_index = indx;
        },
        // удаление группы в форме добавления МН
        del_gr: function(a){
            vm.add_groups_table.splice(a,1);
        },
        // удаление группы в форме редактирования МН
        edit_del_gr: function(a){
            vm.edit_groups_table.splice(a,1); 
        },
        // удаление админ группы в форме добавления МН
        /*del_adm_gr: function(a){  //l-7
            vm.add_adm_groups_table.splice(
                vm.add_adm_groups_table.indexOf(a),
                1
            );
        },
        // добавление админ группы в форме добавления МН
        add_adm_gr: function(gr){
            if(vm.add_adm_groups_table.indexOf(gr) == -1)
                vm.add_adm_groups_table.push(gr);
        },
        // добавление админ группы в форме редактирования МН
        edit_adm_gr: function(gr){
            if(vm.edit_groups_table.indexOf(gr) == -1)
                vm.edit_groups_table.push(gr);
        },*/
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

    //Нажатие на любое место на странице
    $('body').on("click", function(e){
        //..Закрывает меню списка
        if( !$(".mn-btn").is(e.target)
            && 
            $(".mn-btn").has(e.target).length === 0
        ){
            $(".drop-down-menu").hide();
            vm.active_index = 0;
        }
    });

    //Нажатие на "Добавить"
    $("#btn-add-mn").on("click",function(){
        $(this).parent().hide();
        $("#create-form").show();
        $("#main-table").hide();
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
        //vm.add_adm_groups_table = []; //l-7
        $("#main-table").show();
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

    //Нажатие на пункт меню Показать (личные->все->общие)
    
    $("#view_mode").on("click", function(){
        if($(this).data("mode") == "personal"){
            mode = "all";
            $(this).html("Показать общие");
            $(this).data("mode", mode);
            getTable(0);
        }else if($(this).data("mode") == "all"){
            mode = "public";
            $(this).html("Показать личные");
            $(this).data("mode", mode);
            getTable(0);
        }else if($(this).data("mode") == "public"){
            mode = "personal";
            $(this).html("Показать все");
            $(this).data("mode", mode);
            getTable(0);
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
            offset: offset,
            mode: $("#view_mode").data("mode"),
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
                
            if(data.end){
                $("#more_btn").hide();
            }else{
                $("#more_btn").show();
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
            //console.log(arUsers);
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
    
    /*if(vm.mn_type){       //l-7
        for(i in vm.add_adm_groups_table){
            resG.push(vm.add_adm_groups_table[i].id);
        }
    }else{
        for(i in vm.add_groups_table){
            resG.push(vm.add_groups_table[i].value);
        }
    }*/
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
            by_admin: vm.mn_type,
            users: JSON.stringify(res),
            groups: JSON.stringify(resG),
        },
        success: function(data){
            $("#create-form").hide();
            $("#btn-add-mn").parent().show();
            $("#input-name").val("");
            $("#textarea-descr").val("");
            vm.add_users_table = [];
            vm.add_groups_table = [];
            //vm.add_adm_groups_table = []; //l-7
            getTable();
            $("#main-table").show();
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
    //$(".mn-act.active").removeClass("active");
    $(".edit-form").hide();
    $("#main-table").show();
}
//Закрытие формы завершения
function closeDoneForm(){
    vm.edit_users_table = [];
    vm.edit_groups_table = [];
    //$(".mn-act.active").removeClass("active");
    $(".done-form").hide();
    $("#main-table").show();
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
*   Удалить (скрыть) нужду
*/
function deleteMN(indx){
    $.ajax({
        type: "POST",
        url: "/ajax/deleteMN",
        dataType: "json",
        data: {
            _token: $('#x_token').val(),
            id: vm.mainTable[indx].id,
        },
        success: function(data){
            //Удалить нужду из таблицы
            if(data.result == vm.mainTable[indx].id)
                vm.mainTable.splice(indx,1);
        },
        error: function() {
            console.log("deleteMN error");
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
            //try{
                fillAutocomplite(data.groups);
            //}catch{
                //console.log("getGroups data error!");
            //}
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
                label: groups[x].name,
                value: groups[x].id
            });
            vm.user_groups.push({
                label: groups[x].name,
                value: groups[x].id
            });
            /*if(groups[x].admin)       //l-7
            vm.admin_groups.push({
                name: groups[x].name,
                id: groups[x].id
            });*/
        }
    }
    
    vm.is_groups_table = table.length > 0;
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
    $('#input-group').addClass("test");
    //console.log(table);
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

/*
 * Попап с подтверждением удаления
 */
function del_popap(id){
    Swal.fire({
        title: 'Подтвердите удаление',
        text: 'Восстановление невозможно!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ДА, удалить',
        cancelButtonText: 'Отмена'
    }).then((result) => {
        if (result.value) {
            deleteMN(id);
        }
    })
}