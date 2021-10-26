<?php 

add_action('admin_enqueue_scripts',function(){
    wp_enqueue_style('dsi-css-jui', plugin_dir_url(__FILE__) . '../assets/jquery-ui/jquery-ui.css');
    wp_enqueue_style('dsi-css-custom', plugin_dir_url(__FILE__) . '../assets/custom/dsi.css');

    wp_enqueue_script('dsi-js-jq', plugin_dir_url(__FILE__) . '../assets/jquery-ui/jquery-3.6.0.min.js');
    wp_enqueue_script('dsi-js-jui', plugin_dir_url(__FILE__) . '../assets/jquery-ui/jquery-ui.js');
    wp_enqueue_script('dsi-js-custom', plugin_dir_url(__FILE__) . '../assets/custom/dsi.js',Null,'1.0.0',true);
    wp_enqueue_script('dsi-js-jq-custom-global', plugin_dir_url(__FILE__) . '../assets/custom/jq-custom-functions.js',Null,'1.0.0',true);
    wp_enqueue_script('dsi-js-custom-global', plugin_dir_url(__FILE__) . '../assets/custom/global.js',Null,'1.0.0',true);

    wp_localize_script('dsi-js-custom','locsData',array(
        'csv_nonce' => wp_create_nonce('csv_uploads'),
        'admin_url' => get_admin_url(),
        'home_url'=> home_url(),
        'ajax_js_url' => plugin_dir_url('12') . "csv-dropship-import/ajaxes/jScript/"
    ));
})
?>