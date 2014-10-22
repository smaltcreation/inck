$('.btn-tooltip').tooltip();
$('.btn-popover').popover();
$('.carousel').carousel();
function scrollToElement(ele) {
    $(window).scrollTop(ele.offset().top-340).scrollLeft(ele.offset().left);
}

document.onload(scrollToElement($('.form-group.has-error')));