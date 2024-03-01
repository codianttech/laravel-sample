$("#submitBtn").on('click', function(e) {
    e.preventDefault();    
    var frm     = $(this).closest('form');
    var action  = frm.attr('action');
    var type    = frm.attr('method');
    var btn = $('#submitBtn');
    if(frm.valid())
    {
    var showLoader = 'Processing....';
    showButtonLoader(btn, showLoader, 'disabled');
        $.ajax({
            url: action,
            type: type,
            data: frm.serialize(),
            success: function(data) {
               successToaster(data.message);
                setTimeout(function() {
                   location.reload();
                }, 2000);
            },
            error: function(xhr) {
                errorToaster(xhr.responseJSON.message);
            },
        });
    }    
});
