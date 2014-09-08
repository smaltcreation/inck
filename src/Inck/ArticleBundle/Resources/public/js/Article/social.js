$(document).ready(function(){
    var content = $('#content');

    content.on('click', '.vote-up', function(){
        saveVote($(this), 1);
    });

    content.on('click', '.vote-down', function(){
        saveVote($(this), 0);
    });

    function saveVote(clickedButton, up){
        var articleId           = getArticleId(clickedButton),
            group               = clickedButton.closest('.btn-group'),
            buttons             = group.find('button'),
            otherButton         = buttons.not(clickedButton),
            clickedIconClass    = getIconClass(clickedButton),
            otherIconClass      = getIconClass(otherButton),
            icon                = getButtonIcon(clickedButton),
            otherButtonIcon     = getButtonIcon(otherButton);

        buttons.prop('disabled', true);
        icon.attr('class', 'fa fa-circle-o-notch fa-spin');
        // If you want to add style on the otherIcon
//        if(otherButton.hasClass('voted')) {
//            otherButtonIcon.attr('class', 'fa fa-times');
//        }

        // Save the vote
        $.ajax({
            url: Routing.generate('inck_article_vote_new', {
                id: articleId,
                up: up
            }),
            dataType: 'json'
        }).done(function(){
            // Vote saved
            resetButtonState(clickedButton, clickedIconClass);
            resetButtonState(otherButton, otherIconClass);
            var oldVote = (otherButton.hasClass('voted')) ? !up : null;
            otherButton.removeClass('voted');
            clickedButton.toggleClass('voted');
            if(clickedButton.hasClass('voted')) {
                icon.attr('class', 'fa fa-check');
            }
            else {
                if(clickedButton.hasClass('vote-up'))
                    icon.attr('class', 'fa fa-thumbs-up');
                else
                    icon.attr('class', 'fa fa-thumbs-down');
            }
                if(otherButton.hasClass('vote-up'))
                otherButtonIcon.attr('class', 'fa fa-thumbs-up');
            else
                otherButtonIcon.attr('class', 'fa fa-thumbs-down');
            updateScore(clickedButton, up, oldVote);
        }).error(function(result){
            // Error
            resetButtonState(clickedButton, clickedIconClass);
            resetButtonState(otherButton, otherIconClass);
            $('#vote-error').remove();
            var modal = $(result.responseJSON.modal);
            modal.appendTo('body').modal();
        });
    }

    function getArticleId(button){
        return button
            .closest('article')
            .attr('id')
            .replace('article-', '');
    }

    function getIconClass(button){
        return getButtonIcon(button).attr('class');
    }

    function getButtonIcon(button){
        return button.find('i:first');
    }

    function resetButtonState(button, iconClass){
        getButtonIcon(button).attr('class', iconClass);
        button.prop('disabled', false);
    }

    function updateScore(clickedButton, up, oldVote){
        var score           = clickedButton.closest('.social').find('.score:first'),
            progress        = score.find('.progress:first'),
            noProgress      = score.find('.no-progress:first'),
            scoreTotal      = parseInt(progress.attr('data-score-total')),
            progressUp      = progress.find('.score-up'),
            scoreUp         = parseInt(progressUp.attr('data-score-up')),
            progressDown    = progress.find('.score-down'),
            scoreDown       = parseInt(progressDown.attr('data-score-down')),
            value           = (clickedButton.hasClass('voted')) ? 1 : -1;

        if(oldVote != null){
            scoreTotal--;
            if(oldVote == 1){
                scoreUp--;
            } else {
                scoreDown--;
            }
        }

        scoreTotal += value;
        if(up) scoreUp += value;
        else scoreDown += value;

        if(scoreTotal == 0){
            progress.addClass('hidden');
            noProgress.removeClass('hidden').show('slow');
        } else {
            progressUp.css('width', parseInt(scoreUp / scoreTotal * 100) + "%");
            progressDown.css('width', parseInt(scoreDown / scoreTotal * 100) + "%");

            progressUp.find('span:first').text(scoreUp);
            progressDown.find('span:first').text(scoreDown);

            noProgress.addClass('hidden');
            progress.removeClass('hidden').show('slow');
        }

        progress.attr('data-score-total', scoreTotal);
        progressUp.attr('data-score-up', scoreUp);
        progressDown.attr('data-score-down', scoreDown);
    }

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
});