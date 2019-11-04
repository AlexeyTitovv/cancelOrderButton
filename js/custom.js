jQuery(document).ready(function($) {

    $('.cancel').click(function (e) {

    	e.preventDefault();
		var linkCancel = $(this).attr('href');

        $.ajax({
            url: linkCancel,
            success: function(data) {
                $('.woocommerce-orders-table').html($('.woocommerce-orders-table',data).html());
            },
            // error: function () {
            //     alert("Ошибка!");
            // },
        });
    });
});
