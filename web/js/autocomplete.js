useradd = $('#user-add')
$('#user-add-form').submit(function(event) {
    $('#selected-users').append(
        $(' <div class="col-md-1">'+
             '<div class="col-md-12 user label label-default">'+useradd.val()+'</div>'+
            '</div>'
        )
    );
    $('#usermergerecomendationform-users').val( function(i, val) {
        return val + (val ? ' ' : '') + useradd.val()
    });
    useradd.val('');

    event.preventDefault();
    return false;
});
