$(document).ready(function () {
    $('#find-collection').submit(function(event){
        event.preventDefault();
        $.ajax({
            url: $('#find-collection').attr('action'),
            type: 'POST',
            data : $('#find-collection').serialize(),
            success: function(html){
                $('#collection-info').hide().html(html).fadeIn('slow');
            }
        });
        return false;
    });
});