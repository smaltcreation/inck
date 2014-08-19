$(document).ready(function(){
    var input = $("#inck_articlebundle_articlefilter_tags");

    input.select2({
        tags: [],
        tokenSeparators: [",", ";"],
        minimumInputLength: 1,
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
        }
    });

    var reset = $("#inck_articlebundle_articlefilter_actions_cancel");

    reset.click(function(e){
        e.preventDefault();
        var form = $(this).closest("form");
        form.find(".select2-offscreen").val(null).select2("data", null);
        form.submit();
    });
});