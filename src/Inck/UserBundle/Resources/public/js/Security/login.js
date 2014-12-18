$(function() {
    var BV = new $.BigVideo();
    BV.init();
    if (Modernizr.touch) {
        BV.show('video-poster.jpg');
    } else {
        BV.show('bundles/inckuser/video/drink.mp4',{ambient:true});
    }
});