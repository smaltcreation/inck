$(document).ready(function(){
    // Toggle filters
    $('.toggle-filter').click(function(){
        $('#filters').stop().slideToggle('slow');
    });

    // Selected filters
    selectedFilters = $.parseJSON(selectedFilters);

    // Categories filter
    var categoriesInput = $("#inck_articlebundle_articlefilter_categories");

    categoriesInput.select2({
        width: 'container'
    });

    reset_categories();

    // Tags filter
    var tagsInput = $("#inck_articlebundle_articlefilter_tags");

    tagsInput.select2({
        tags: [],
        tokenSeparators: [",", ";"],
        minimumInputLength: 1,
        width: 'container',
        query: function(query){
            if(query.term == '') return;
            $.ajax({
                url: Routing.generate('inck_article_tag_autocomplete', {
                    mode: 'id',
                    name: query.term
                }),
                dataType: 'json'
            }).done(function(data){
                query.callback(data);
            });
        }
    });

    reset_tags();

    // Authors filter
    var authorsInput = $("#inck_articlebundle_articlefilter_authors");

    authorsInput.select2({
        tags: [],
        tokenSeparators: [",", ";"],
        minimumInputLength: 1,
        width: 'container',
        query: function(query){
            if(query.term == '') return;
            $.ajax({
                url: Routing.generate('inck_user_user_autocomplete', {
                    username: query.term
                }),
                dataType: 'json'
            }).done(function(data){
                query.callback(data);
            });
        }
    });

    reset_authors();

    // Form
    var form = $('form[name="inck_articlebundle_articlefilter"]');

    // Submit filters
    form.submit(function(e){
        e.preventDefault();
        var timeline = $('#timeline').find('div.articles:first');

        $.ajax({
            url: Routing.generate('inck_article_article_filter'),
            data: {
                filters: {
                    categories: categoriesInput.val(),
                    tags: tagsInput.val(),
                    authors: authorsInput.val()
                }
            },
            method: 'POST',
            dataType: 'html'
        }).done(function(articles){
            timeline.replaceWith(articles);

            $('#articles-total').find('span:first').text(
                $('#timeline').find('.articles:first').attr('data-total-articles')
            );
        });

        return false;
    });

    // Reset filters
    function reset_categories(){
        if('category' in selectedFilters){
            categoriesInput.select2('data', [selectedFilters.category]);
        }
    }

    function reset_tags(){
        if('tag' in selectedFilters){
            tagsInput.select2('data', [selectedFilters.tag]);
        }
    }

    function reset_authors(){
        if('author' in selectedFilters){
            authorsInput.select2('data', [selectedFilters.author]);
        }
    }

    var reset = $("#inck_articlebundle_articlefilter_actions_cancel");

    reset.click(function(e){
        e.preventDefault();
        form.find(".select2-offscreen").val(null).select2("data", null);
        reset_categories();
        reset_tags();
        reset_authors();
        form.submit();
    });

    // Pagination
    var nextPage = 2;
    var totalPages = $('#timeline').find('div.articles:first').attr('data-total-pages');

    $('#timeline-next-page').click(function(){

    });
});