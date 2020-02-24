var vm = new Vue({
    el: '#PE',
    data: {
        MNid: [],
        groups: {},
        group_name: {},
        authors: [],
        admin: [],      //массив ИД МН добавленных администратором
    },
    methods:{
        getMorePrayers(d){
            getPrayersList(d,0);
        }
    },
});
let arMN = {};

$( document ).ready(function(){
    getPrayersList("",0);

});

/*
*   Получение МН
*/
function getPrayersList(last_date="", group_id=0){
    globalAjax(
        '/ajax/getPrayersList',
        {
            last_date: last_date,
            group_id: group_id
        },
        function(data){
            try{
                console.log(data);
                //console.table(data.MN);
                for(i in data.MN){
                    vm.MNid.push(data.MN[i].id);
                    arMN[data.MN[i].id] = data.MN[i];
                }
                for(x in data.mn_groups)
                    vm.groups[x] = data.mn_groups[x];
                for(z in data.groups)
                    vm.group_name[z] = data.groups[z];
                for(y in data.authors)
                    vm.authors[y] = data.authors[y];
                for(a in data.admin)
                    vm.admin[a] = data.admin[a];
                if(data.MN.length < 30){
                    $("#more_btn").hide();
                }
            }catch(e){
                console.log(e);
            }
        },
        function(){}
    );
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

/*
Сколько прошло дней или дата (не используется)
*/
let now = new Date();
function dayAgo(сdate){
    let cDate = new Date(сdate);
    let objDateFormat = {
        month: "long",
        day: "numeric"
    };
    let time_lost = '';
    let diff = Math.floor((now.getTime() - cDate.getTime())/1000);

    if(diff > 24*3600*365){
        objDateFormat['year'] = "numeric";
    }else if(diff < now.getHours()*3600){
        time_lost = "сегодня";
    }else if(diff < (now.getHours()*3600 + 24*3600)){
        time_lost = "вчера";
    }else if(diff < 4*24*3600){
        time_lost = Math.floor(diff/(24*3600))+" дня назад";
    }else if(diff < 10*24*3600){
        time_lost = Math.floor(diff/(24*3600))+" дней назад";
    }
    let formatter = new Intl.DateTimeFormat("ru", objDateFormat);
    if(!time_lost) time_lost = formatter.format(cDate);

    return time_lost;
}