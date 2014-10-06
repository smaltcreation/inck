$(document).ready(function(){
    clampedWidth();

    function clampedWidth(){
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
    }

    // Affix
    var score = $('#article-score');
    setAffix();

    function setAffix(){
        var offset = null;

        score
            .affix({
                offset: {
                    top: function(){
                        return $('#header').outerHeight() - 50;
                    },
                    bottom: function(){
                        return $('#footer').find('footer:first').outerHeight() + 100;
                    }
                }
            })
            .on('affix.bs.affix', function(){
                if(offset === null){
                    offset = score.offset().left;
                }
            }).on('affixed.bs.affix affixed-top.bs.affix affixed-bottom.bs.affix', function(){
                score.offset({
                    left: offset
                });
            })
        ;
    }

    // Resize
    function windowResized(){
        $(window).off('.affix');
        score
            .removeData('bs.affix')
            .removeClass('affix affix-top affix-bottom')
            .removeAttr('style');
        clampedWidth();
    }

    var resizing;
    window.onresize = function(){
        clearTimeout(resizing);
        resizing = setTimeout(windowResized, 100);
    };
});