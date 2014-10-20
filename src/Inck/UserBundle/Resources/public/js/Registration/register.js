function scrollToElement(ele) {
    $(window).scrollTop(ele.offset().top-340).scrollLeft(ele.offset().left);
}

document.onload(scrollToElement($('.has-error')));