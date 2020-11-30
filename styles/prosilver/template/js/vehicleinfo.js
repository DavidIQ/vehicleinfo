$(function() {
    let $vi_loadingIndicator = null;
    const vehicleinfo = {
        handleError: function(err) {
            if (!!err.responseJSON && !!err.responseJSON.message) {
                alert(err.responseJSON.message);
            }
            else {
                alert(!!err.statusText ? err.statusText : (!!err.responseText ? err.responseText : err));
            }
        },
        handleAlways: function() {
            if ($vi_loadingIndicator && $vi_loadingIndicator.is(':visible')) {
                $vi_loadingIndicator.fadeOut(phpbb.alertTime);
            }
        }
    };

    $('#vi_make').on('change', function() {
        $vi_loadingIndicator = phpbb.loadingIndicator();
        const $this = $(this);
        const $vi_model = $('#vi_model');
        $vi_model.children('option').remove();
        $vi_model.append($("<option></option>")
            .text($this.attr('data-loading-text') + '...')
        );

        $.ajax({
            url: $this.attr('data-ajax-action') + parseInt($this.val()),
            type: 'GET',
            success: function(response) {
                $vi_model.children('option').remove();
                $vi_model.append($("<option></option>"));
                response.forEach(model => {
                    $vi_model.append($("<option></option>")
                       .attr('value', model.id)
                       .text(model.name));
                });
            },
            error: vehicleinfo.handleError,
            cache: false
        }).always(vehicleinfo.handleAlways);
    })

    const $mark_sold = $('.mark-sold'),
          $unmark_sold = $('.unmark-sold');

    $mark_sold.on('click', function() {
        $vi_loadingIndicator = phpbb.loadingIndicator();
        const $this = $(this);
        $.ajax({
            url: $this.attr('data-ajax-action') + parseInt($this.attr('data-value')),
            type: 'POST',
            success: function(response) {
                $mark_sold.hide();
                $unmark_sold.show();
                $unmark_sold.children('span').text(response);
            },
            error: vehicleinfo.handleError,
            cache: false
        }).always(vehicleinfo.handleAlways);
    });

    $unmark_sold.on('click', function() {
        $vi_loadingIndicator = phpbb.loadingIndicator();
        const $this = $(this);
        $.ajax({
            url: $this.attr('data-ajax-action') + parseInt($this.attr('data-value')),
            type: 'POST',
            success: function(response) {
                $mark_sold.show();
                $unmark_sold.hide();
            },
            error: vehicleinfo.handleError,
            cache: false
        }).always(vehicleinfo.handleAlways);
    });
});