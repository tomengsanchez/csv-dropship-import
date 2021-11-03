<?php 

/**
 * Loads the Necessary Class/Files in the System
 */
Class DSI_Loader{
    public function __construct()
    {
        //echo"fffffffff";
        $this->load_controller('DSI_Main_Controller');
        $this->load_controller('DSI_Dropship_Import_Settings');
        $this->load_controller('DSI_Dropship_Providers');
        $this->load_controller('DSI_Db');

        $this->load_controller('DSI_Products');
        $this->load_files();
    }
    public function load_files(){
        // loads the Plugin Management Class
        include_once  __DIR__ . "/dsi_plugin_mgmt.php";
        include_once  __DIR__ . "/dsi_enqueue.php";
        include_once  __DIR__ . "/dsi_admin_menu.php";
        include_once  __DIR__ . "/dsi_custon_functions.php";
        // foreach(glob("/../ajaxes/*.php") as $filename){
        //     include_once  __DIR__ . $filename;
        // }
        //Ajaxes

        include_once  __DIR__ . "/../ajaxes/DSI_Providers_Page_ajax.php";
        include_once  __DIR__ . "/../ajaxes/Main-Page_ajax.php";
        include_once  __DIR__ . "/../ajaxes/DSI_Settings_View_ajax.php";
        include_once  __DIR__ . "/ajaxes.php";
    }
    public function load_controller($cntrlr){
        include_once  __DIR__ . "/../DSI_Controllers/$cntrlr" . ".php";
         
    }
    /**
     * Loads the View
     * @param $views : Filename
     */
    public function load_view($views, $data = array()){
        include_once  __DIR__ . "/../DSI_Views/$views" . ".php";
    }
    public function test(){
        echo "121";
    }

}

?>