$('[data-target="modal"]').click(function(e) {
    e.preventDefault();
    var url = $(this).attr("href");
    if (url.indexOf("#") == 0) {
        $(url).modal('open');
    } else {
        $.get(url, function(data) {
            $('<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' + data + '</div>').modal().on('hidden', function(){
                $('.modal-backdrop.in').each(function(i) {
                    $(this).remove();
                });
            });
        }).success(function() { $('input:text:visible:first').focus(); });
    }
});