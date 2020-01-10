var vm = new Vue({
    el: '#appV',
    data: {
        mainTable: [],
        add_users_table: [],
        edit: {},           //Объект - редактируемая в данный момент нужда
        done: {},           //Завершаемая в данный момент нужда
        text: '',
        arUsers: [],
        edit_users_table: [],
        is_thanks: 0,           //Является ли нужда - благодарностью
        
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
            }
        },
        show_done_form(indx, event){
            if($(event.target).hasClass("active")){
                closeDoneForm();
            }else{
                //Если опубликована благодарность то ее можно только закрыть
                if(vm.mainTable[indx].is_thanks){
                    saveDoneForm(true);
                    return true;
                }
                closeEditForm();//на всякий случай прячем форму редактирования
                
                $(event.target).addClass("active");
                $("#container-"+indx).append($(".done-form"));
                $(".done-form").show();
                vm.done = vm.mainTable[indx];
            }
        }
    },
    computed: {
        /*needs: function () {
            return this.mainTable.map(function (obj) {
                if(!obj.description_show)
                    obj.description_show = false;
                else obj.description_show = !obj.description_show;
                obj.edit_show = false;
                obj.done_show = false;
                return obj;
            })
        }*/
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
    //Открытие формы редактирования или удаления
    /*$(".mn-act").on("click",function(){
        var id = $(this).parents(".t-tr").data("mnid");
        var act = $(this).data("act");
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
            $("#container-"+id+">div").hide();   //Убераем формы которые остались показаны если была нажата
            $(this).siblings(".mn-act.active").removeClass("active"); //кнопка редактировать сразу после завершить
            $(this).addClass("active");
            $("."+act+"-form")
                .show()
                .appendTo($("#container-"+id));
            
            MNeditID = id;
            if(act == "done" && MN.is_thanks == 1){
                saveDoneForm(true);
                return true;
            }
            //Вставка значений
            $("#name-"+act).val($(this).parents(".t-tr").find(".t-name").html());
            $("#descr-"+act).val($(this).parents(".mn-item").find(".mn-description>p").html());
            
            vm.edit_users_table = MN.users;
            
            if(MN.is_thanks == 1){
                $("#name-edit").prop("readonly", true);
                $("#descr-edit").prop("readonly", true);
                $("#result-edit-form").show();
                $("#result-edit").val(MN.answer);
                $("#share-edit").hide();
            }else{
                $("#name-edit").prop("readonly", false);
                $("#descr-edit").prop("readonly", false);
                $("#result-edit-form").hide();
                $("#result-edit").val("");
                $("#share-edit").show();
            }
            $("#result-done").removeClass("is-invalid");
            $("#container-"+id).show();
        }
    });*/
    
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
            mainTable = data;
            for(x in data)
                vm.mainTable.push(data[x]);
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
    var res = [];
    for(i in vm.add_users_table){
        res.push(vm.add_users_table[i].value);
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
            $("#btn-add-mn").parent().show();
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
    $("#result-edit").val("");
    vm.edit_users_table = [];
    $(".mn-act.active").removeClass("active");
    $(".edit-form").hide();
    //$("#container-"+MNeditID).hide();
    //MNeditID = 0;
}
//Закрытие формы завершения
function closeDoneForm(){
    vm.edit_users_table = [];
    $(".mn-act.active").removeClass("active");
    $(".done-form").hide();
    //$("#container-"+MNeditID).hide();
    //MNeditID = 0;
}
/*
 * Завершение публикации молитвы с возможностью добавить результат
 * и опубликовать как благодарность
 * param done - завершить молитву без указания результата и переопубликования.
 */
function saveDoneForm(done = false){
    var result;
    var re_publish;
    if(done){
        result = "";
        re_publish = 0;
    }else{
        result = vm.done.answer;
        re_publish = vm.done.is_thanks;
    }
    if(re_publish == 1 && result == ""){
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
            id: vm.done.id,
            result: result,
            re_publish: re_publish
        },
        success: function(data){
            //Изменение значений в таблице и закрытие формы редактирования
            closeDoneForm();
            if(!re_publish) $(".t-tr[data-mnid="+id+"]").hide();
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
            //id: MNeditID,
            id: vm.edit.id,
            name: name,
            //text: description,
            text: vm.edit.description,
            result: vm.edit.result,
            users: JSON.stringify(res)
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
