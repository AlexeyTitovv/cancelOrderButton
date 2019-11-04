<?php
/**
 * Plugin Name:       Test AJAX
 */



add_action('wp_enqueue_scripts', 'my_assets');
function my_assets() {
    wp_enqueue_script('custom', plugins_url('js/custom.js', __FILE__), array('jquery'));

    wp_localize_script('custom', 'myPlugin', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}

add_filter( 'woocommerce_valid_order_statuses_for_cancel', 'custom_valid_order_statuses_for_cancel', 10, 2 );

function custom_valid_order_statuses_for_cancel( $statuses, $order ){

    $custom_statuses    = array( 'processing' );

    return $custom_statuses;
}

function my_custom( $order ) {

    if ( ! is_object( $order ) ) {
        $order_id = absint( $order );
        $order    = wc_get_order( $order_id );
    }

    $actions = array(
        'pay'    => array(
            'url'  => $order->get_checkout_payment_url(),
            'name' => __( 'Pay', 'woocommerce' ),
        ),
        'view'   => array(
            'url'  => $order->get_view_order_url(),
            'name' => __( 'View', 'woocommerce' ),
        ),

    );

    $cancel = array(
        'cancel' => array(
            'url'  => $order->get_cancel_order_url( '/my-account/orders/' ),
            'name' => __( 'Отказаться   ', 'woocommerce' ),
        ),
    );

    $items = $order->get_items();

    foreach ($items as $item) {

        $product_id = $item->get_product_id();

        $products = wc_get_product($product_id);

//        if ($products->stock_status == 'outofstock') {
            $actions = array_merge($actions, $cancel);
//        }

    }

    if ( ! $order->needs_payment() ) {
        unset( $actions['pay'] );
    }

    if ( ! in_array( $order->get_status(), apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ), true ) ) {
        unset( $actions['cancel'] );
    }

    return apply_filters( 'woocommerce_my_account_my_orders_actions', $actions, $order );
}


function myplugin_plugin_path() {

    return untrailingslashit( plugin_dir_path( __FILE__ ) );
}
add_filter( 'woocommerce_locate_template', 'myplugin_woocommerce_locate_template', 10, 3 );



function myplugin_woocommerce_locate_template( $template, $template_name, $template_path ) {
    global $woocommerce;

    $_template = $template;

    if ( ! $template_path ) $template_path = $woocommerce->template_url;

    $plugin_path  = myplugin_plugin_path() . '/woocommerce/';

    $template = locate_template(

        array(
            $template_path . $template_name,
            $template_name
        )
    );

    if ( ! $template && file_exists( $plugin_path . $template_name ) )
        $template = $plugin_path . $template_name;

    if ( ! $template )
        $template = $_template;

    return $template;
}

add_filter( 'wc_order_statuses', 'wc_renaming_order_status' );
function wc_renaming_order_status( $order_statuses ) {
    foreach ( $order_statuses as $key => $status ) {
        if ( 'wc-cancelled' === $key )
            $order_statuses['wc-cancelled'] = _x( 'Клиент отказался отзаказа', 'Order status', 'woocommerce' );
    }
    return $order_statuses;
}
