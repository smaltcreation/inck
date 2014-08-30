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

    resetCategories();

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

    resetTags();

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

    resetAuthors();

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
            resetPaginator();

            $('#articles-total').find('span:first').text(
                $('#timeline').find('.articles:first').attr('data-total-articles')
            );
        });

        return false;
    });

    // Reset filters
    function resetCategories(){
        if('category' in selectedFilters){
            categoriesInput.select2('data', [selectedFilters.category]);
        }
    }

    function resetTags(){
        if('tag' in selectedFilters){
            tagsInput.select2('data', [selectedFilters.tag]);
        }
    }

    function resetAuthors(){
        if('author' in selectedFilters){
            authorsInput.select2('data', [selectedFilters.author]);
        }
    }

    var reset = $("#inck_articlebundle_articlefilter_actions_cancel");

    reset.click(function(e){
        e.preventDefault();

        form.find(".select2-offscreen").val(null).select2("data", null);
        resetCategories();
        resetTags();
        resetAuthors();

        form.submit();
    });

    // Pagination
    var nextPage = 2;

    function resetPaginator(){
        $('#timeline-next-page').removeClass('hidden');
        nextPage = 2;
    }

    $('#timeline-next-page').click(function(){
        var button = $(this);
        var icon = button.find('i:first');
        icon.attr('class', 'fa fa-circle-o-notch fa-spin');

        $.ajax({
            url: Routing.generate('inck_article_article_filter', {
                page: nextPage
            }),
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
            nextPage++;
            var timeline = $('#timeline');
            var list = timeline.find('div.articles:first');
            var totalPages = list.attr('data-total-pages');

            if(nextPage > totalPages){
                button.addClass('hidden');
            }

            $(articles).find('article').each(function(){
                $(this).appendTo(list);
            });

            icon.attr('class', 'fa fa-plus');
        });
    });
});