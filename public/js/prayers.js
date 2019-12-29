var vm = new Vue({
    el: '#appV',
    data: {
        table: [],
        text: '',
        arUsers: [],
        edit_users_table: [],
        is_thanks: 0,           //Является ли нужда - благодарностью
    },
    methods:{
        pri: function(){
            this.table.aa = "77";
            //console.log(vm.table);
        },
        del: function(a){
            vm.table.splice(a,1);
        }
    },
    computed: {
    }
});

var arUsers = {}; //Список всех пользователей
var mainTable;      //Основная таблица МН
var MNeditID;       //ID элемента, который в данный момнет редактируется

$( document ).ready(function(){
    
    getTable();
    //Получение списка пользователей для "поделится"
    getUsers();

    //Нажатие на "Добавить"
    $("#btn-add-mn").on("click",function(){
        $(this).hide();
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
        $("#btn-add-mn").show();
        $("#input-name").val("");
        $("#textarea-descr").val("");
        $("#input-user").val("");
        vm.table = [];
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
            $(this).html("Показать описание");
            $("#desc-"+$(this).parents("tr.main").data("mnid")).hide();
        }else{
            $(this).addClass("active");
            $(this).html("Скрыть описание");
            $("#desc-"+$(this).parents("tr.main").data("mnid")).show();
        }
    });
    //Открытие формы редактирования или удаления
    $(".mn-act").on("click",function(){
        var id = $(this).parents("tr.main").data("mnid");
        var act = $(this).data("act");
        console.log(id+" / "+act);
        var MN;
        for(x in mainTable){
                if(mainTable[x].id == id){
                    MN = mainTable[x];      //Текущая нужда
                    break;
                }
        }
        if($(this).hasClass("active")){
            $(this).removeClass("active");
            $("."+act+"-form").hide();
            $("#container-"+id).hide();  //скрываем контейнер для формы редактирования/удаления
            MNeditID = 0;
        }else{
            $("#container-td-"+id+">div").hide();   //Убераем формы которые остались показаны если была нажата
            $(this).siblings(".mn-act.active").removeClass("active"); //кнопка редактировать сразу после завершить
            $(this).addClass("active");
            $("."+act+"-form")
                .show()
                .appendTo($("#container-td-"+id));
            
            MNeditID = id;
            if(act == "done" && MN.is_thanks == 1){
                saveDoneForm(true);
                return true;
            }
            //Вставка значений
            $("#name-"+act).val($(this).parents("tr.main").find("td[title='Название']").html());
            $("#descr-"+act).val($("#desc-"+$(this).parents("tr.main").data("mnid")).find("td").html());
            
            vm.edit_users_table = MN.users;
            
            if(MN.is_thanks == 1){
                $("#name-edit").prop("readonly", true);
                $("#descr-edit").prop("readonly", true);
                $("#result-egit-form").show();
                $("#result-egit").val(MN.answer);
            }else{
                $("#name-edit").prop("readonly", false);
                $("#descr-edit").prop("readonly", false);
                $("#result-egit-form").hide();
                $("#result-egit").val("");
            }
            $("#result-done").removeClass("is-invalid");
            $("#container-"+id).show();
        }
    });
    
});

function getTable(){
    $.ajax({
        type: "POST",
        url: "/ajax/getTable",
        dataType: "json",
        headers: {
                'X-CSRF-TOKEN': $('#x_token').val()
            },
        data: {
            aaa: 'some string'
        },
        success: function(data){
            mainTable = data;
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
                    vm.table.push(ui.item);
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
    var res = [];
    for(i in vm.table){
        res.push(vm.table[i].value);
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
            users: JSON.stringify(res)
        },
        success: function(data){
            $("#create-form").hide();
            $("#btn-add-mn").show();
            console.log(data);
            console.log(data.success);
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
    vm.edit_users_table = [];
    $(".mn-act.active").removeClass("active");
    $(".edit-form").hide();
    $("#container-"+MNeditID).hide();
    MNeditID = 0;
}
//Закрытие формы завершения
function closeDoneForm(){
    vm.edit_users_table = [];
    $(".mn-act.active").removeClass("active");
    $(".done-form").hide();
    $("#container-"+MNeditID).hide();
    MNeditID = 0;
}
function saveDoneForm(done = false){
    var result;
    var re_publish;
    if(done){
        result = "";
        re_publish = 0;
    }else{
        result = $("#result-done").val();
        re_publish = $("#thankfulness").prop("checked")?1:0;
    }
    if(re_publish == true && result == ""){
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
            id: MNeditID,
            result: result,
            re_publish: re_publish
        },
        success: function(data){
            //Изменение значений в таблице и закрытие формы редактирования
            closeDoneForm();
            if(!re_publish) $("tr.main[data-mnid="+id+"]").hide();
        },
        error: function(data) {
            console.log("doneMN error");
            console.log(data);
        }
    });
}

function editMN(){
    // Находим массив ID пользователи, с которыми поделились молитвой
    var res = [];
    var name = $("#name-edit").val();
    if(!name || name.length < 3){
        $("#name-edit").addClass("is-invalid");
        $("#name-edit").one("change", function(){
            $("#name-edit").removeClass("is-invalid");
        });
        return false;
    }
    var description = $("#descr-edit").val();
    for(i in vm.edit_users_table){
        res.push(vm.edit_users_table[i].id);
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
            id: MNeditID,
            name: name,
            text: description,
            users: JSON.stringify(res)
        },
        success: function(data){
            //Изменение значений в таблице и закрытие формы редактирования
            $("tr.main[data-mnid='"+MNeditID+"'] td.main-name").html(name);
            $("#desc-"+MNeditID+" td").html(description);
            closeEditForm();
        },
        error: function() {
            console.log("editMN error");
        }
    });
}
