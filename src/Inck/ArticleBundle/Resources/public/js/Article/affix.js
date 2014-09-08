$(document).ready(function(){
    /*
     * Clamped-width.
     * Usage:
     *  <div data-clampedwidth=".myParent">This long content will force clamped width</div>
     *
     * Author: LV
     */
    $('[data-clampedwidth]').each(function () {
        var elem = $(this);
        //elem.attr('data-offset-bottom', $('article').height());
        var parentPanel = elem.data('clampedwidth');
        var resizeFn = function () {
            var sideBarNavWidth = $(parentPanel).width() - parseInt(elem.css('paddingLeft')) - parseInt(elem.css('paddingRight')) - parseInt(elem.css('marginLeft')) - parseInt(elem.css('marginRight')) - parseInt(elem.css('borderLeftWidth')) - parseInt(elem.css('borderRightWidth'));
            elem.css('width', sideBarNavWidth);
        };

        resizeFn();
        $(window).resize(resizeFn);
    });

    // Affix
    var social = $('#article-social');
    var offset = null;

    social.affix({
        offset: {
            top: function(){
                return $('#header').outerHeight() - 50;
            },
            bottom: function(){
                return $('#footer').find('footer:first').outerHeight() + 100;
            }
        }
    }).on('affix.bs.affix', function(){
        if(offset == null){
            offset = social.offset().left;
        }
    }).on('affixed.bs.affix affixed-top.bs.affix affixed-bottom.bs.affix', function(){
        social.offset({
            left: offset
        });
    });

    // Resize
    function windowResized(){
        offset = social.offset().left;
    }

    var resizing;
    window.onresize = function(){
        clearTimeout(resizing);
        resizing = setTimeout(windowResized, 100);
    };
});