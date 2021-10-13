<?php 
/**
 * Class For Managing The Plugin
 */
class DSI_Plugin_mgmt{
    public function __construct()
    {  
        
    }
    public function dsi_activation(){
        add_option( 'ds_developer','tomeng' );
    }
    public function deactivation(){
        delete_option( 'dsi_developer');
    }
    public function delete(){
        
    }
    /** activation function for create roles and Capabilities */
    
}





?>