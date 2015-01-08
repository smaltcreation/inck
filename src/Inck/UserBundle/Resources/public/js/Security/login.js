$(function() {
    var BV = new $.BigVideo({useFlashForFirefox:false});
    BV.init();

    if (Modernizr.touch) {
        BV.show('bundles/inckuser/images/drink.png');
    } else {
        BV.show([
            { type: "video/mp4",  src: "bundles/inckuser/video/drink.mp4" },
            { type: "video/ogg",  src: "bundles/inckuser/video/drink.ogv" }
        ],{ambient:true});

        BV.getPlayer().on('durationchange',function(){
            $('#big-video-wrap').fadeIn();
        });
    }
});
