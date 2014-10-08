$(function() {
    $(window).scroll(function(){
        var distanceTop = $('#last').offset().top - $(window).height();
        if  ($(window).scrollTop() > distanceTop)
            $('#slidebox').animate({'right':'0px'},300);
        else
            $('#slidebox').stop(true).animate({'right':'-430px'},100);
        var distanceProgress = ($('#last').offset().top - $(window).height());
        var articleProgressWidth = ($('#article-progress-bar').offset().top / distanceProgress);
        document.getElementById('article-progress-bar').style.width=(articleProgressWidth*100)+"%";
});

$('.close-slidebox').bind('click',function(){
        $(this).parent().remove();
    });
});