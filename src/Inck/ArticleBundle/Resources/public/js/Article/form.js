$(document).ready(function(){
    var input = $("#inck_articlebundle_article_tags");

    input.select2({
        tags: [],
        tokenSeparators: [",", ";"],
        minimumInputLength: 1,
        maximumSelectionSize: 10,
        width: 'container',
        initSelection : function(element, callback){
            var tags = input.val().split(',');
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
        },
        createSearchChoice: function(term){
            return { id: term, text: term };
        }
    });
});