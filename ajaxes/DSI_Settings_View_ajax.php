<?php 
/**
 * Ajax File of the DSI Settings View
 * Page : page=dropship-import-settings
 * File : DSI_Settings_View.php
*/


add_action('wp_ajax_get_dsi_api_credentials',function(){
    header('Content-Type:application/json');
    if(!wp_verify_nonce($_REQUEST['_nonce'],'csv_uploads')){
        
       echo $output = json_encode(array('message'=>'Security Error'));
        exit();
    }
    $output = json_encode(
        [
            'message'=>$_POST['_nonce'],
            'ck'=>get_option('dsi_wc_ck'),
            'cs'=>get_option('dsi_wc_cs')
        ]       
    );
    echo $output;
    exit();
    
});

add_action('wp_ajax_set_credentials',function(){
    print_r($_POST);
    update_option('dsi_wc_ck',$_POST['ck']);
    update_option('dsi_wc_cs',$_POST['cs']);
    exit();
});



?>