<?php 
/**
 * This is the Classes for the Products
 */
class DSI_Products extends DSI_Loader{
    /** product information  */
    var $product_info =array();
    var $product_fields = array();
    var $field_type = array();
    public function __construct(){
        
    }

    public function send_product_fields(){
        $this->product_fields = ['a','b','c'];
    }
    public function get_wc_product_fields(){
        $args = array(
            'post_type'=>'product'
        );
        $product_query = new WP_Query($args);
        $result = $product_query->get_meta_keys();
    }
    
    public function DSI_insert_products(){
        
    }
    public function DSI_get_imported($cols = array()){
        return $cols;
    }
}
?>