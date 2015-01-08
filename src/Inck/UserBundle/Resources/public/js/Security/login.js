$(function() {
    var BV = new $.BigVideo();
    BV.init();

    if (Modernizr.touch) {
        BV.show('bundles/inckuser/images/drink.mp4');
    } else {
        BV.show('bundles/inckuser/video/drink.mp4',{ambient:true});
        BV.getPlayer().on('durationchange',function(){
            $('#big-video-wrap').fadeIn();
        });
    }
});
