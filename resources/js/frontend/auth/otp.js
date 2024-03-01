$(document).on('click', '#signInBtn', function (e) {
    e.preventDefault();
    var form = $('#admin-otp-login-form')
    if(form.valid()) { 
        elementEnableDisabled('#signInBtn',true);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (response)
            {
                if(response.success){
                    successToaster(response.message);
                    setTimeout(function () {
                        window.location.href = response.redirectionUrl;
                    }, 1000);
                }else{
                    errorToaster(response.message,'Login');
                }
            }, error: function (err) {
                
                handleError(err);
            }, complete : function (){
                elementEnableDisabled('#signInBtn',false);
            }
        });
    }
});

$(document).on('click', "#resentBtn", function(e){
    e.preventDefault();
    let url = $(this).data('href');
    elementEnableDisabled('#resentBtn',true);
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response)
        {
            if(response.success){
                successToaster(response.message);
            }else{
                errorToaster(response.message,'OTP');
            }
        }, error: function (err) {
            handleError(err);
        }, complete : function (){
            elementEnableDisabled('#resentBtn',false);
        }
    });
});