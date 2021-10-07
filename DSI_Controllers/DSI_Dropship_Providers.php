<?php 
/**
 * the Main Controller for Dropship Provider Page page=dropship-providers
 */
class DSI_Dropship_Providers extends DSI_Loader{
    public function __construct()
    {  
       //echo "Hello DropShippers"; 
    }
    /**
     * Loading the View of the Page , 
     */
    public function load_ds_provider_view(){
        $this->load_view('DSI_Dropship_Providers_Views');
    }
}
?>
