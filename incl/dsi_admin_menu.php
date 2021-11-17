<?php
/**
 * Manage the Admin Menu
 */


 /**
  * Displays the Admin Menu of Dropship Import
  */
add_action ('admin_menu',function(){
    add_menu_page( 'Dropship Import CSV' , 'Dropship Import',  'manage_options', 'dropship-import-page', 'dsi_connect_main' , 'dashicons-upload');
});
/**
  * Function Displays the Admin Menu of Dropship Import
  */
function dsI_connect_main(){
   $main_controller = new DSI_Main_Controller();
   $main_controller->load_ds_main_page();
}

/** Displays the Admin Submenu of Dropship Providers */

add_action('admin_menu',function(){
    add_submenu_page( 'dropship-import-page', 'Dropship Providers', 'Dropship Providers', 'manage_options', 'dropship-providers', 'dashboard_dropship_provider');
});
/** Functions Displays the Admin Submenu of Dropship Providers */
function dashboard_dropship_provider(){
   $dsi_providers = new DSI_Dropship_Providers(); 
   $dsi_providers->load_ds_provider_view();
}

/** Displays the Admin Submenu of Settings */
add_action('admin_menu',function(){
    add_submenu_page( 'dropship-import-page', 'Settings', 'Settings', 'manage_options', 'dropship-import-settings', 'dashboard_import_settings');
});
/** Functions Displays the Admin Submenu of Settings */
function dashboard_import_settings(){
    $dsi_settings = new DSI_Dropship_Import_Settings();
    $dsi_settings->ds_load_settings_view();

}


?>