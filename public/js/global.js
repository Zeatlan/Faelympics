$(document).ready(function() {
    $(this).scroll(function(){
        if ($(this).scrollTop() > 10) {
            $('.navbar').addClass('scrolled');
        } else {
            $('.navbar').removeClass('scrolled');
        }
    });

    $("a[href='#down']").on('click',function(event) {
        $("html, body").animate({ scrollTop: $(document).height() }, 700);
        return false;
    });

    $("a[href='#top']").on('click',function(event) {
        $("html, body").animate({ scrollTop: 0 }, 700);
        return false;
    });

});