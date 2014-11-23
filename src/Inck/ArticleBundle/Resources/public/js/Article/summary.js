$('.btn-report').click(function(){
    var btn = $(this);
    var icon = btn.find('i:first');
    icon.attr('class', 'fa fa-circle-o-notch fa-spin');

    $.ajax({
        url: Routing.generate('inck_article_report_new', {
            id: btn.closest('article').attr('id').replace('article-', '')
        }),
        dataType: 'json'
    }).done(function() {
        icon.attr('class', 'fa fa-flag');
        btn.toggleClass('reported');

        var span = btn.find('span.total:first');
        var reports = parseInt(span.text());

        span.text(
            (btn.hasClass('reported'))
                ? reports + 1
                : reports - 1
        );
    });
});
