<?php 
/**
 * This is the Classes for the Products
 * 
 * @param array $collect_meta_to_import - collect all the final meta/field before import
 * 
 * 
 */
class DSI_Products extends DSI_Loader{
    /** product information  */
    /** collect all the final meta/field before import */
    var $collect_meta_to_import = array();

    public function __construct(){
        
    }
    
    function get_all_wc_default_field(){
        $default_field = array(
            'post_title',
            'post_content',
            'post_status',
            'post_type,',
            'product_type',
            '_visibility',
            'total_sales',
            '_downloadable',
            '_virtual',
            '_sale_price',
            '_regular_price',
            '_featured',
            '_weight',
            '_length',
            '_width',
            '_height',
            '_sku',
            '_product_attributes',
            '_sale_price_dates_from',
            '_sale_price_dtes_to',
            '_price',
            '_sold_individually',
            '_backorders',
            '_stock',
        );
        return $default_field;
    }

    /**
     * Set All the Default Field of Wcommerce product
     * @param array $field_collection -- collects the array of the desired field first 5 param should be 'post_title','post_content','post_status','post_type,','product_type'
     * 
     */
    function set_meta_to_import($field_collection = array()){
        if(!$field_collection){
            $this->collect_meta_to_import = $this->get_all_wc_default_field();
        }
        else{
            $this->collect_meta_to_import = $field_collection;
        }
    }
    /**
     * Assigns the product meta field to the specific solumn of the csv
     * @param array $data - array of metafield and converted meta field format from csv headingarray('meta_field','_')
     * @param array $heading array of the heading of the csv file
     * )
     * 
     */
    function ds_assign_column($data,$heading){
        $assigned_field_column = array();
        $x = 0;
        foreach ($data as $d){
            $column_number = 0;// get The Column Number of the Assigned Converted Heading
            for($i = 0; $i< count($heading); $i++ ){
                if(conv_string_to_meta( $heading[$i]) == $d[1]){ // Check if converted headings of assigned and converted csv headings are the same
                    $column_number = $i;//assig for column number
                    //echo $heading[$i];
                }
            }
            array_push($assigned_field_column,array($d[0] . "isthe - wc_product_meta",$column_number . " is the csv_col#"));// will make the assigned colum and columng number to search
            $x++;
        }
        return $assigned_field_column;
    }
    /**
     * 
     */
}
?>