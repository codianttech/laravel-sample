<script nonce="{{ csp_nonce('script') }}">


// window scroll event header
$(window).scroll(function() {
    if ($(window).scrollTop() >= 50) {
        $('.header').addClass('fixedHeader');
        $('body').addClass('removeBg');
    } else {
        $('.header').removeClass('fixedHeader');
        $('body').removeClass('removeBg');
    }
});

//ripple-effect for button
$('.ripple-effect, .ripple-effect-dark').on('click', function(e) {
    var rippleDiv = $('<span class="ripple-overlay">'),
        rippleOffset = $(this).offset(),
        rippleY = e.pageY - rippleOffset.top,
        rippleX = e.pageX - rippleOffset.left;
    rippleDiv.css({
        top: rippleY - (rippleDiv.height() / 2),
        left: rippleX - (rippleDiv.width() / 2),
    }).appendTo($(this));
    window.setTimeout(function() {
        rippleDiv.remove();
    }, 800);
});


// Body padding according to header height
$(document).ready(function() {
    var headerHeight = $('.header').outerHeight();
    $('body').css('padding-top', headerHeight + 'px');
    $(window).resize(function() {
        var headerHeight = $('.header').outerHeight();
        $('body').css('padding-top', headerHeight + 'px');
    })
});

//Progressive Image
progressively.init();
</script>
