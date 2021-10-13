<?php 
/**
 * Class for Settings
 * @attributes strings $api_url URL of the api
 * @attributes strings $api_cnsumer_key consumer key
 * @attributes strings $api_consumer_secret consumer secret
 */
class DSI_Dropship_Import_Settings extends DSI_Loader{
    var $api_url = '';
    var $api_consumer_key = '';
    var $api_consumer_secret = '';

    public function __construct()
    {
        
    }
    /** 
     * Set The Woocomerce Api Credentials
     * 
     * @param string $url URL of the API
     * @param string $ck Comsumer key of the API
     * @param string $cs Comsuner Secret of the API
     */
    public function set_wc_api_api($url = '', $ck = '',$cs = ''){
        $this->api_url = $url;
        $this->api_consumer_key = $ck;
        $this->api_consumer_secret = $cs;
    }
    /**
     * 
     */
    public function dsi_wc_api_connect(){

    }
    /**
     * Loads the Settings View
     */
    public function ds_load_settings_view(){
        $this->load_view('DSI_Settings_View');
    }

}
?>