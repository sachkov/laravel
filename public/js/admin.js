let tableLength = 0;        //Длинна текущей таблицы
let tableName = '';
$( document ).ready(function(){
    
});
/*
 * Запрос на получение содержимого выбранной таблицы
 */
function show_table(name){
    console.log(name);
    tableName = '';
    //отправка ajax
    $.ajax({
        type: "POST",
        url: "/admin/getTable",
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('#x_token').val()
        },
        data: {
            _token: $('#x_token').val(),
            name: name,
        },
        success: function(data){
            try{
            console.log(data);
            tableLength = data.count;
            tableName = name;
            
            //do table
            $("#main_table").html(createTable(data.table));
            }catch{
                console.log("show_table data error!");
            }
        },
        error: function(data) {
            console.log("show_table error");
            console.log(data);
        }
    });
}
/*
 * Функция создания html таблицы на основе входного массива
 */
function createTable(data){
    let html = '<table class="table">';
    for(x in data){
        html += '<tr>';
        if(x == 0){
            for(y in data[0]){
                html += '<th>'+y+'</th>';
            }
            html += '<th>Delete</th></tr><tr>';
        }
        for(z in data[x]){
            html += '<td>'+data[x][z]+'</td>';
        }
        html += '<td class="point rd" onclick="del('+data[x].id+')"><span>X</span></td></tr>';
    }
    html += '</table>';
    return html;
}

/*
 * Попап с подтверждением удаления
 */
function del(id){
    Swal.fire({
        title: 'Точно удалить запись',
        text: "Отменить не получится!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ДА, удалить',
        cancelButtonText: 'Отмена'
    }).then((result) => {
        if (result.value) {
            trueDel(id);
        }
    })
}
/*
 * Запрос на удаление записи из БД
 */
function trueDel(id=0){
    console.log(id);
    if(!tableName || tableName=='' || !id){
        console.log('trueDel input data error');
        return false;
    }
    $.ajax({
        type: "POST",
        url: "/admin/delTableRow",
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('#x_token').val()
        },
        data: {
            _token: $('#x_token').val(),
            name: tableName,
            id: id
        },
        success: function(data){
            if(data) console.log("del success");
            else console.log("del error");
        },
        error: function(data) {
            console.log("delTableRow error");
            console.log(data);
        }
    });
}

