
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
            console.log(data);
        },
        error: function(data) {
            console.log("error");
            console.log(data);
        }
    });
}


