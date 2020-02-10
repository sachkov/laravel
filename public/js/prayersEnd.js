var vm = new Vue({
    el: '#PE',
    data: {
        mainTable: [],
    },
    methods:{
        edit_del_gr: function(a){
            vm.edit_groups_table.splice(a,1);
        },
    },
});

$( document ).ready(function(){
    
});

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