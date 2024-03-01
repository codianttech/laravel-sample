$(document).on('click', '#resetBtn', function (e) {
    e.preventDefault();
    var form = $('#add-form')
    if(form.valid()) { 
        elementEnableDisabled('#resetBtn',true);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (response)
            {
                if(response.success){
                    successToaster(response.message);
                    setTimeout(function () {
                        window.location.href = response.data.redirectionUrl;
                    }, 1000);
                }else{
                    errorToaster(response.message,'Reset Password');
                }
            }, error: function (err) {
                handleError(err);
            }, complete : function (){
                elementEnableDisabled('#resetBtn',false);
            }
        });
    }
});
