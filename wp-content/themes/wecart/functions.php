<?php

remove_action( 'in_admin_header', 'wp_admin_bar_render', 0 );

//disables top margin
add_filter( 'admin_title', function(){ $GLOBALS['wp_query']->is_embed=true;  add_action('admin_xml_ns', function(){ $GLOBALS['wp_query']->is_embed=false; } ); } );

// Init Profile Options
function wpdocs_register_my_custom_menu_page() {
    add_menu_page(
        __( 'Perfil', 'wecart' ),
        'Perfil',
        'manage_woocommerce',
        'settings/profile.php',
        '',
        'dashicons-store',
        80
    );
}

add_action( 'admin_menu', 'wpdocs_register_my_custom_menu_page' );
// End Profile Options