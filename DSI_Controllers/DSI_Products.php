<?php 
/**
 * This is the Classes for the Products
 * 
 * @param array $collect_meta_to_import - collect all the final meta/field before import
 * 
 * 
 * 
 */

require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;


class DSI_Products extends DSI_Loader{
    /** product information  */
    /** collect all the final meta/field before import */

    var $products = array();
    var $collect_meta_to_import = array();
    var $data_per_lines = array();

    var $product_type = '';
    var $product_id = '';
    var $product_type_others = array(
        'default' => 'simple'
    );
    var $dropship_company = array();

    var $image = array();

    var $final_fields_to_import = array();
    var $final_values_to_import = array();
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
     * 
     * @return array $assigned_field_column collection of assigned field column
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
            // will make the assigned colum and columng number to search
            array_push($assigned_field_column,array($d[0],$column_number));
            $x++;
        }
        return $assigned_field_column;
    }
    /**
     * Read the file please use fo() and this function will set $data_per_lines to each lines
     * 
     * @param file file data $_FILE['name']['tmpname']
     */ 
    function read_csv_lines($file){
        while(!feof($file)){
            $lines = fgetcsv($file);
            array_push($this->data_per_lines,$lines);
        }
    }
    /**
     * Import CSV to Woocommerce Products
     *  
     * @param array $column_assignment - collection of default_column and the csv_column number example : 'post_title'=> [10]
     * @param array $lines csv_files columns and rows
     * 
     * 
     */
    function import_csv_to_wc($column_assigment= array(),$lines = array()){    
        echo count($lines);   
        

        for($x = 0; $x < count($lines); $x++){//loop each line
            //loop each field
            echo "Impor these line " . $x;

            echo "<br>";
            echo "<table border=1>";
            echo "<tr>";
            
            echo "<th>Index</th>";
            echo "<th>Meta VAlue</th>";
            echo "<th>Csv Column Number</th>";
            echo "<th>Meta Value</th>";
            echo "</tr>";
            for($i = 0; $i < count($column_assigment);$i++ ){// loop each field

                echo "<tr>";    
                echo "<td> " . $i. "</td>";
                echo "<td> " . $column_assigment[$i][0]. "</td>";
                echo "<td> " . $column_assigment[$i][1]. "</td>";
                array_push($this->final_fields_to_import,$column_assigment[$i][0]);
                $val = '';
                if($column_assigment[$i][1] == 0){
                    $val = 'Please Add Another meta field using : ' . $column_assigment[$i][0] . "-". $val = $lines[$x][$column_assigment[$i][1]];
                }   
                else
                    $val = $lines[$x][$column_assigment[$i][1]];
                    if($i == 4){
                        $val = $this->product_type_others['default'];
                    }
                echo "<td>";
                    echo $val;
                    array_push($this->final_values_to_import,$val);

                echo "</td>";
                echo "</tr>";    
            }
            echo "</table>";
        }
    }
    /**
     * This function will create products using rest
     * 
     * @param array $field final fields
     * @param array $value final values 
     * 
     * 'ck_0ba977f283e08a5bc62bd3947cfe4a3c705e9c49','cs_d67316e5ce91a56b2d753732d4a54cd2f9a33e13'
     */

    public function dsi_create_products_rest(){
        $woocommerce = new Client(
            'http://localhost/ud/',
            get_option('dsi_wc_ck'),
            get_option('dsi_wc_cs'),
            [
                'wp_api' => true,
                'version' => 'wc/v3'
            ]

            ); 
        //ar_to_pre($woocommerce->get('products'));
        
        $data = [
            'name' => 'Premium Quality',
            'type' => 'simple',
            '_regular_price' => '21.99',
            'description' => 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.',
            'short_description' => 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
            'categories' => [
                [
                    'id' => 9
                ],
                [
                    'id' => 14 
                ]
            ],
            'images' => [
                [
                    'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_front.jpg'
                ],
                [
                    'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_back.jpg'
                ]
            ]
        ];
        ar_to_pre($data);
        
        ar_to_pre($woocommerce->post('products', $data));
    }

    /**
     * This will create products using wpdb
     */

     public function dsi_create_products_wpdb($data_per_lines = array()){
        //ar_to_pre($data_per_lines);
        for($i = 1; $i < count($data_per_lines); $i++){// loop each line
            $in = $i -1;
            //ar_to_pre($data_per_lines[$i]);
            $post_id = wp_insert_post( array(
                
                'post_title' => $data_per_lines[$in][10],
                'post_content' => $data_per_lines[$in][16],
                'post_status' => 'publish',
                'post_type' => "product",
            ) );
            wp_set_object_terms( $post_id, 'simple', 'product_type' );
            
            update_post_meta( $post_id, '_visibility', 'visible' );
            update_post_meta( $post_id, '_stock_status', 'instock');
            update_post_meta( $post_id, 'total_sales', '0' );
            update_post_meta( $post_id, '_downloadable', 'no' );
            update_post_meta( $post_id, '_virtual', 'yes' );
            update_post_meta( $post_id, '_regular_price', $data_per_lines[$in][11] );
            update_post_meta( $post_id, '_sale_price', $data_per_lines[$in][6] );
            update_post_meta( $post_id, '_purchase_note', '' );
            update_post_meta( $post_id, '_featured', 'no' );
            update_post_meta( $post_id, '_weight', '' );
            update_post_meta( $post_id, '_length', '' );
            update_post_meta( $post_id, '_width', '' );
            update_post_meta( $post_id, '_height', '' );
            update_post_meta( $post_id, '_sku', $data_per_lines[$in][1] );
            update_post_meta( $post_id, '_product_attributes', array() );
            update_post_meta( $post_id, 'sale_price_dates_from', '' );
            update_post_meta( $post_id, 'sale_price_dates_to', '' );
            update_post_meta( $post_id, '_price', $data_per_lines[$in][6] );
            update_post_meta( $post_id, '_sold_individually', '' );
            update_post_meta( $post_id, '_manage_stock', 'no' );
            update_post_meta( $post_id, '_backorders', 'no' );
            update_post_meta( $post_id, '_stock', '' );
            echo "DONE";
        }
    }
     /**
      * For the Mean Time This will be used for AW Dropship
      */
     public function dsi_create_products_rest_aw_dropship($data_per_lines = array()){
        $created = 0;
        $updated = 0;
        $woocommerce = new Client(
        'http://localhost/ud/',
        get_option('dsi_wc_ck'),
        get_option('dsi_wc_cs'),
        [
            'wp_api' => true,
            'version' => 'wc/v3'
        ]

        ); 
      
        for($i = 1; $i < count($data_per_lines); $i++){// loop each line

            $in = $i-1;
            //echo $data_per_lines[$i][23];echo "<br>";
            $imgs =array();
            if($data_per_lines[$in][23]){
                array_push($imgs,
                    ['src' => $data_per_lines[$in][23]]
                );
            }
            if($data_per_lines[$in][24]){
                array_push($imgs,
                    ['src' => $data_per_lines[$in][24]]
                );
            }
            if($data_per_lines[$in][25]){
                array_push($imgs,
                    ['src' => $data_per_lines[$in][25]]
                );
            }
            if($data_per_lines[$in][26]){
                array_push($imgs,
                    ['src' => $data_per_lines[$in][26]]
                );
            }

            $data = [
                'name' => $data_per_lines[$in][10],
                'type' => 'simple',
                'regular_price' => $data_per_lines[$in][6],
                'description' => $data_per_lines[$in][16],
                'short_description' => $data_per_lines[$in][16],
                'categories' => [
                    [
                        'id' => 9
                    ],
                    [
                        'id' => 14 
                    ]
                ],
                'images'=>$imgs,
                'sku'=> $data_per_lines[$in][1]
            ];
            //echo "Done";
            //print_r($woocommerce->get('products'));
            //check if sku existing

            $existing = $woocommerce->get('products',['sku'=>$data_per_lines[$in][1]]);
            //echo $data_per_lines[$in][1];
            if(count($existing) <= 0){
                //echo "insert";
                $woocommerce->post('products', $data);  
                $created++;
            }
            else{
                //echo $existing[0][1];
                $woocommerce->put('products/' . $existing[0]->id, $data);
                //ar_to_pre($existing);
                $updated++;
            }
        }
        echo "<h4>" . $created . " rows Created and " . $updated. " rows Updated</h4>";
    }
}
?>