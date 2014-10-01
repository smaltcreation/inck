$(document).ready(function(){
    // Toggle filters
    var toggleFilters = $('.toggle-filter');
    toggleFilters.click(function(){
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
                    input: query.term
                }),
                dataType: 'json'
            }).done(function(data){
                query.callback(data);
            });
        }
    });

    resetAuthors();

    // Order filter
    var orderInput = $("#inck_articlebundle_articlefilter_order");
    orderInput.val('date');

    orderInput.select2({
        width: 'container'
    });

    // Form
    var form = $('form[name="inck_articlebundle_articlefilter"]');
    var articlesTotal = $('#articles-total');
    var submitButtonClicked = true;
    var submitButton = $('#inck_articlebundle_articlefilter_actions_filter');
    var resetButton = $('#inck_articlebundle_articlefilter_actions_cancel');

    // Submit filters
    form.submit(function(e){
        e.preventDefault();

        var timeline = $('#timeline').find('div.articles:first');
        articlesTotal.stop().fadeOut();

        var buttonContent;
        submitButton.add(resetButton).prop('disabled', true);

        if(submitButtonClicked){
            buttonContent = submitButton.html();
            submitButton.html('<i class="fa fa-circle-o-notch fa-spin"></i> Chargement...');
        } else {
            buttonContent = resetButton.html();
            resetButton.html('<i class="fa fa-circle-o-notch fa-spin"></i> Chargement...');
        }

        timeline.find('article').each(function(index){
            $(this).delay(400 * index).slideUp(300);
        });

        timeline.promise().done(function(){
            $.ajax({
                url: Routing.generate('inck_article_article_filter'),
                data: {
                    filters: {
                        categories: categoriesInput.val(),
                        tags: tagsInput.val(),
                        authors: authorsInput.val(),
                        order: orderInput.val(),
                        search: $("#inck_articlebundle_articlefilter_search").val()
                    }
                },
                method: 'POST',
                dataType: 'html'
            }).done(function(articles){
                articles = $(articles);
                articles.find('article').hide();

                timeline.replaceWith(articles);
                timeline = $('#timeline');

                timeline.find('article').each(function(index){
                    $(this).delay(400 * index).slideDown(300);
                });

                resetPaginator();

                var totalArticles   = timeline.find('.articles:first').attr('data-total-articles');
                var totalPages      = timeline.find('.articles:first').attr('data-total-pages');
                articlesTotal.find('span:first').text(totalArticles);
                articlesTotal.stop().fadeIn();
                submitButton.add(resetButton).prop('disabled', false);

                if(submitButtonClicked){
                    submitButton.html(buttonContent);
                } else {
                    resetButton.html(buttonContent);
                }

                if(totalPages <= 1){
                    $('#timeline-next-page').addClass('hidden');
                }

                submitButtonClicked = true;
            });
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

        submitButtonClicked = false;
        form.submit();
        toggleFilters.click();
    });

    // Pagination
    var nextPage = 2;

    function resetPaginator(){
        $('#timeline-next-page').removeClass('hidden');
        nextPage = 2;
    }

    $('#timeline-next-page').click(getNextPage);

    function getNextPage(){
        var button = $(this);
        if (button.prop('disabled')) return false;
        button.prop('disabled', true);

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
                    authors: authorsInput.val(),
                    order: orderInput.val(),
                    search: $("#inck_articlebundle_articlefilter_search").val()
                }
            },
            method: 'POST',
            dataType: 'html'
        }).done(function (articles) {
            nextPage++;

            var timeline = $('#timeline');
            var list = timeline.find('div.articles:first');
            var totalPages = list.attr('data-total-pages');

            if (nextPage > totalPages) {
                button.addClass('hidden');
            }

            $(articles).find('article').each(function () {
                $(this).appendTo(list);
            });

            icon.attr('class', 'fa fa-plus');
            button.prop('disabled', false);
        });

        return true;
    }
});