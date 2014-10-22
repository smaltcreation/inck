$(document).ready(function(){
    $('.btn-tooltip').tooltip();
    $('.btn-popover').popover();
    $('.carousel').carousel();

    var error = $('.form-group.has-error:first');
    if(error.length !== 0) {
        scrollToElement(error);
    }
});

function scrollToElement(ele) {
    $(window).scrollTop(ele.offset().top-340).scrollLeft(ele.offset().left);
}
