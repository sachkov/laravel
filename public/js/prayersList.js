var vm = new Vue({
    el: '#PE',
    data: {
        arMN: [],
        groups: [],
        group_name: [],
        authors: [],
    },
    methods:{
        edit_del_gr: function(a){
            vm.edit_groups_table.splice(a,1);
        },
    },
});

$( document ).ready(function(){
    globalAjax(
        '/ajax/getPrayersList',
        {
            last_date: '',
            group_id: 0
        },
        function(data){
            try{
                //console.log(data);
                //console.table(data.MN);
                vm.arMN = data.MN;
                vm.mn_groups = data.mn_groups;
                vm.group_name = data.groups;
                vm.authors = data.authors;
            }catch(e){
                console.log(e);
            }
            
        },
        function(){}
    );
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