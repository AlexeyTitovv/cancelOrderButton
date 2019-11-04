jQuery(document).ready(function($) {

    $('.cancel').click(function (e) {

    	e.preventDefault();
		var linkCancel = $(this).attr('href');

        $.ajax({
            url: linkCancel,
            success: function(data) {
                // var result = $('<div />').append(data).find('.woocommerce-orders-table').html();
                // $('.woocommerce-orders-table').html(result);
                $('.woocommerce-orders-table').html($('.woocommerce-orders-table',data).html());
            }
        });
    });
});
