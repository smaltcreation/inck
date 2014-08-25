$(document).ready(function(){
    // Toggle filters
    $('.toggle-filter').click(function(){
        $('#filters').slideToggle('slow');
    });

    // Categories filter
    var categoriesInput = $("#inck_articlebundle_articlefilter_categories");
    categoriesInput.select2({
        width: 'container'
    });

    // Tags filter
    var tagsInput = $("#inck_articlebundle_articlefilter_tags");

    tagsInput.select2({
        tags: [],
        tokenSeparators: [",", ";"],
        minimumInputLength: 1,
        width: 'container',
        initSelection : function(element, callback){
            var tags = tagsInput.val().split(',');
            var data = [];

            $.each(tags, function(key, value){
                data.push({
                    id: value,
                    text: value
                });
            });

            callback(data);
        },
        query: function(query){
            if(query.term == '') return;
            $.ajax({
                url: Routing.generate('inck_article_tag_autocomplete', {
                    name: query.term
                }),
                dataType: 'json'
            }).done(function(data){
                query.callback(data);
            });
        }
    });

    // Form
    var form = $('form[name="inck_articlebundle_articlefilter"]');
    var timeline = $('#timeline').find('div.articles:first');

    // Submit filters
    form.submit(function(e){
        e.preventDefault();
        timeline.find('article').slideUp('slow');

        $.ajax({
            url: Routing.generate('inck_article_article_filter'),
            data: {
                filters: {
                    type: $('#inck_articlebundle_articlefilter_type').val(),
                    categories: categoriesInput.val(),
                    tags: tagsInput.val()
                }
            },
            method: 'POST',
            dataType: 'html'
        }).done(function(articles){
            articles = $(articles);
            articles.find('article').hide();
            timeline.html(articles);
            articles.find('article').slideDown();
        });

        return false;
    });

    // Reset filters
    var reset = $("#inck_articlebundle_articlefilter_actions_cancel");

    reset.click(function(e){
        e.preventDefault();

        form.find(".select2-offscreen").val(null).select2("data", null);
        form.submit();
    });
});