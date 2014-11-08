function similarArticles(){
    var articleContent = $('body').find('.article-content');
    var cumulatedHeight = articleContent.height() + $('.article-summary').height();

    $('.article-similar').css("height", (cumulatedHeight + 170));
    $(".article-preview").each(function(){
        if (($(this).offset().top) > ($('#last').offset().top)) {
            $(this).hide();
        }
    });
}

$(document).ready(function(){
    similarArticles();

    $(window).scroll(function(){
        var last = $('#last'), slidebox = $('#slidebox');
        var distanceTop = last.offset().top - $(window).height();

        if ($(window).scrollTop() > distanceTop) {
            slidebox.animate({
                'right': '20px'
            }, 300);
        }

        else {
            slidebox.stop(true).animate({
                'right': '-430px'
            }, 100);
        }

        var distanceProgress = last.offset().top - $(window).height();
        var progressBar = $('#article-progress-bar');
        var articleProgressWidth = progressBar.offset().top / distanceProgress;

        progressBar.css('width', (articleProgressWidth * 100) + '%');
    });

    $('.close-slidebox').click(function(){
        $(this).parent().remove();
    });

    $(window).bind('resizeEnd', function(){
        similarArticles();
    });

    $(window).resize(function(){
        if(this.resizeTO) clearTimeout(this.resizeTO);
        this.resizeTO = setTimeout(function() {
            $(this).trigger('resizeEnd');
        }, 500);
    });
});
