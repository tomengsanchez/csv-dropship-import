<?php 
/** 
 * Main Controller for the Main-PAge
 * 
 * 
 */


class DSI_Main_Controller extends DSI_Loader{
    public $jsonreturn;
    public $response = array();
    public function __construct()
    {
        //$this->ajax_get_sample();
    }
    /**
     * Use to load DS Main Page
     * 
     * 
     * @param string $x - tomeng
     */
    public function load_ds_main_page($x = ''){
        //$this->get_data();
        $data['1'] ='qw';
        //$data['sample'] = 'tomeng';

        $this->load_view('Main-Page');
        add_action('admin_notices',function(){
            printf( '<div class="updated"> <p> %s </p> </div>', esc_html__( 'This is a yellow notice', 'dropship-import-page' ) );
        });
        
    }

    function laod_main_view(){
        
    }

    

    
}
?>