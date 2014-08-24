$(document).ready(function(){
    // Toggle filters
    $('.toggle-filter').click(function(){
        var jumbotron = $('#jumbotron-filter');
        if(jumbotron.is(':visible')){
            jumbotron.hide('slow');
        } else {
            jumbotron.show('slow');
        }
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

    // Reset filters
    var reset = $("#inck_articlebundle_articlefilter_actions_cancel");

    reset.click(function(e){
        e.preventDefault();
        var form = $(this).closest("form");
        form.find(".select2-offscreen").val(null).select2("data", null);
        form.submit();
    });
});