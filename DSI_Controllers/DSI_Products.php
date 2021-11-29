<?php 
/**
 * This is the Classes for the Products
 * 
 * @param array $collect_meta_to_import - collect all the final meta/field before import
 * 
 */
//require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;
//use Dompdf\FrameDecorator\Image;

class DSI_Products extends DSI_Loader{
    /** product information  */
    /** collect all the final meta/field before import */
    var $con;
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
    var $valid_name_heading = array('Name','Unit Name');
    var $wc_api;
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

        //return $this->data_per_lines;
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
    /** 
     * This will insert product via WC_Product_Simple
     * 
     * @param array @data_per_lines
     */


    public function dsi_wc_product_simple($prod){
        //ar_to_pre($prod);
        

        // CREATE PRODUCTS
        $objProduct = new WC_Product();
        $objProduct->set_name($prod['name']);
        $objProduct->set_regular_price($prod['price']);
        $objProduct->set_sale_price($prod['sale_price']);
        $objProduct->set_status('publish');
        $objProduct->set_sku($prod['sku']);
        $objProduct->set_description($prod['description']);
        $objProduct->set_length($prod['length']);
        $objProduct->set_width($prod['width']);
        $objProduct->set_height($prod['height']);
        $objProduct->set_weight($prod['weight']);
        
        $objProduct->set_category_ids(array($prod['category']));
        //$objProduct->set_image_id($prod);

        
        // GET PRODUCT ID
        //$prod_id = $objProduct->save();

        


        // SET THUMBNAIL TO POST
        
        // Add Featured Image to Post

        
        if($prod['thumbnail'] != null){
            $image_url        = $prod['thumbnail']; // Define the image URL here
            $image_name       = 'main.png';
            $upload_dir       = wp_upload_dir(); // Set upload folder
            $image_data       = file_get_contents($image_url); // Get image data
            $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
            $filename         = basename( $unique_file_name ); // Create image file name

            // Check folder permission and define file location
            if( wp_mkdir_p( $upload_dir['path'] ) ) {
                $file = $upload_dir['path'] . '/' . $filename;
            } else {
                $file = $upload_dir['basedir'] . '/' . $filename;
            }

            // Create the image  file on the server
            file_put_contents( $file, $image_data );

            // Check image file type
            $wp_filetype = wp_check_filetype( $filename, null );

            // Set attachment data
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => sanitize_file_name( $filename ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            // Create the attachment
            $attach_id = wp_insert_attachment( $attachment, $file);

            // Include image.php
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Define attachment metadata
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

            // Assign metadata to attachment
            wp_update_attachment_metadata( $attach_id, $attach_data );

            // And finally assign featured image to post
            
            $objProduct->set_image_id($attach_id);
            //set_post_thumbnail( $post_id, $attach_id );
        }


        //Image 2
        if($prod['thumbnail2'] != null){

            $image_url        = $prod['thumbnail2']; // Define the image URL here
            $image_name       = '2nd.png';
            $upload_dir       = wp_upload_dir(); // Set upload folder
            $image_data       = file_get_contents($image_url); // Get image data
            $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
            $filename         = basename( $unique_file_name ); // Create image file name

            // Check folder permission and define file location
            if( wp_mkdir_p( $upload_dir['path'] ) ) {
                $file = $upload_dir['path'] . '/' . $filename;
            } else {
                $file = $upload_dir['basedir'] . '/' . $filename;
            }

            // Create the image  file on the server
            file_put_contents( $file, $image_data );

            // Check image file type
            $wp_filetype = wp_check_filetype( $filename, null );

            // Set attachment data
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => sanitize_file_name( $filename ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            // Create the attachment
            $attach_id = wp_insert_attachment( $attachment, $file);

            // Include image.php
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Define attachment metadata
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

            // Assign metadata to attachment
            wp_update_attachment_metadata( $attach_id, $attach_data );

            // And finally assign featured image to post
            
            //set_post_thumbnail( $post_id, $attach_id );
            $objProduct->set_gallery_image_ids([$attach_id]);
        }
        // save
        return $objProduct->save();
       
       
        //echo "Good";

        
    }


    /** 
     * This will insert product via WC_Product_Simple_bulk
     * 
     * @param array @data_per_lines 
     */


    public function dsi_wc_product_simple_bulk_create(){
        //ar_to_pre($this->final_values_to_import);
        $lines = $this->data_per_lines;
        //ar_to_pre($lines);
        for($l = 1; $l < count($lines); $l++){
            $this->dsi_wc_product_simple(
                [
                    'name'=>$lines[$l-1][10],
                    'sku'=>$lines[$l-1][1],
                    'thumbnail'=>$lines[$l-1][23],
                    'thumbnail2'=>$lines[$l-1][24],
                    'description'=>$lines[$l-1][16]
                ]
            );  
        }
        
        
        //ar_to_pre($objProduct);
    }

    public function dsi_wc_product_simple_update($prod){

        $objProduct = new WC_Product($prod['id']);
        $objProduct->set_name($prod['name']);
        $objProduct->set_regular_price($prod['price']);
        $objProduct->set_status('publish');
        $objProduct->set_sku($prod['sku']);
        $objProduct->set_description($prod['description']);
        $objProduct->set_length($prod['length']);
        $objProduct->set_width($prod['width']);
        $objProduct->set_height($prod['height']);
        $objProduct->set_weight($prod['weight']);
        $objProduct->set_category_ids(array($prod['category']));

        //$objProduct->set_image_id($prod);

        
        // GET PRODUCT ID
        //$prod_id = $objProduct->save();

        


        // SET THUMBNAIL TO POST
        
        // Add Featured Image to Post

        
        if($prod['thumbnail'] != null){
            $image_url        = $prod['thumbnail']; // Define the image URL here
            $image_name       = 'main.png';
            $upload_dir       = wp_upload_dir(); // Set upload folder
            $image_data       = file_get_contents($image_url); // Get image data
            $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
            $filename         = basename( $unique_file_name ); // Create image file name

            // Check folder permission and define file location
            if( wp_mkdir_p( $upload_dir['path'] ) ) {
                $file = $upload_dir['path'] . '/' . $filename;
            } else {
                $file = $upload_dir['basedir'] . '/' . $filename;
            }

            // Create the image  file on the server
            file_put_contents( $file, $image_data );

            // Check image file type
            $wp_filetype = wp_check_filetype( $filename, null );

            // Set attachment data
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => sanitize_file_name( $filename ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            // Create the attachment
            $attach_id = wp_insert_attachment( $attachment, $file);

            // Include image.php
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Define attachment metadata
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

            // Assign metadata to attachment
            wp_update_attachment_metadata( $attach_id, $attach_data );

            // And finally assign featured image to post
            
            $objProduct->set_image_id($attach_id);
            //set_post_thumbnail( $post_id, $attach_id );
        }
        



        //Image 2
        if($prod['thumbnail2'] != null ){

            $image_url        = $prod['thumbnail2']; // Define the image URL here
            $image_name       = '2nd.png';
            $upload_dir       = wp_upload_dir(); // Set upload folder
            $image_data       = file_get_contents($image_url); // Get image data
            $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
            $filename         = basename( $unique_file_name ); // Create image file name

            // Check folder permission and define file location
            if( wp_mkdir_p( $upload_dir['path'] ) ) {
                $file = $upload_dir['path'] . '/' . $filename;
            } else {
                $file = $upload_dir['basedir'] . '/' . $filename;
            }

            // Create the image  file on the server
            file_put_contents( $file, $image_data );

            // Check image file type
            $wp_filetype = wp_check_filetype( $filename, null );

            // Set attachment data
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => sanitize_file_name( $filename ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            // Create the attachment
            $attach_id = wp_insert_attachment( $attachment, $file);

            // Include image.php
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Define attachment metadata
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

            // Assign metadata to attachment
            wp_update_attachment_metadata( $attach_id, $attach_data );

            // And finally assign featured image to post
            
            //set_post_thumbnail( $post_id, $attach_id );
            $objProduct->set_gallery_image_ids([$attach_id]);
        }
        // save
        return $objProduct->save();

        
        //echo "Good";
    }
    /**
     *  Manipulate Category Add and Create Existing Product
     * 
     */
    function category_manipulation($cat,$parent = ''){
        //pag wala tong laman wag
        $cat_args = array(
            'hide_empty' => false,
            'name'=>$cat
        );
        $cats_result = get_terms('product_cat',$cat_args);
        if(count($cats_result) == 0)  {
            wp_insert_category([
                'taxonomy'=>'product_cat',
                'cat_name'=>$cat,
                'cat_description' => '',
                'category_parent' => $parent
            ]);
            
        }
        else{
            //echo "This is existing : " . $cats[$x]. "<br>"; 
            
        }
        return $this->get_product_category_id($cat);
    }
    function get_product_category_id($cat_name){
        $cat_args = array(
            'hide_empty' => false,
            'name'=>$cat_name
        );
        $res = get_terms('product_cat',$cat_args);
        return $res[0]->term_id;
    
    }
    /** Insert Category for nested values 
     *  @param string $category category strings from a csv value format like 'default\child1\child2\child3,default\child1\child2\child3,
    */
    function category_manipulation_nested($category){
        
        //remove '/'
        $categories = array();
        $processed_cat = explode(',',$category); // removed the ','
        foreach($processed_cat as $cat){
            $sliced_cat = explode('/',$cat); // removed the '/'
            $parent = '';
            //ar_to_pre($sliced_cat);
            for($x =0 ;$x < count($sliced_cat) ; $x++){
                if($x >0)
                    $parent = $this->category_manipulation($sliced_cat[$x-1]);
                $categ_id =  $this->category_manipulation($sliced_cat[$x],$parent);
                array_push($categories,$categ_id);
            }
        }
        // ','
        // ','
        return $categories;
        
        
    }

    /** 
     * Create Products Through RAW SQL
     * 
     */
    function connect_db(){
        $this->con = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

    }
    function insert_product_raw_sql($table, $args){
        global $wpdb;
        $this->connect_db();
        //$dsidb = new DSI_Db();

        //check slug
        $post_name = sanitize_title($args['name']);

        $sqlSelectSlug = "
            SELECT * FROM " . $wpdb->prefix. "posts
            WHERE post_name = '" . $post_name . "'
        ";

        
        
        $r = $this->con->query($sqlSelectSlug);
        if($r->num_rows > 0){
            $post_name = $post_name . "-" . ($r->num_rows + 1);
        }
        
        // insert to post

        $sqlInsert = "
            INSERT INTO " . $wpdb->prefix."posts
            (
                post_author,
                post_date,
                post_date_gmt,
                post_modified,
                post_modified_gmt,
                post_content,
                post_title,
                post_excerpt,
                post_status,
                post_name,
                post_type,
                to_ping,
                pinged,
                post_content_filtered,
                post_parent,
                post_mime_type

            )
            VALUES
            (
                '" . get_current_user_id(). "', /*post_author*/
                '" .  date('Y-m-d H:i:s') . "',/*post_date*/
                '" .  date('Y-m-d H:i:s') . "', /*post_date_gmt*/
                '" .  date('Y-m-d H:i:s') . "', /*post_modified*/
                '" .  date('Y-m-d H:i:s') . "',/*post_modified_gmt*/
                '" . $args['description'] . "',/*post_content*/
                '" . $args['name'] . "', /*post_title*/
                '', /*post_author*/
                'publish', /*post_status*/
                '" . $post_name . "', /*post_name*/
                'product', /*post_type*/
                ' ', /*to_ping*/
                ' ', /*pinged*/
                ' ', /*post_content_filtered*/
                0, /*post_parent*/
                ' ' /*post_mime_type*/
            )
        ";

        $this->con->query($sqlInsert);
        $product_id = $this->con->insert_id;
        $post_id = $product_id;
        //Upate the taxonomy
        if(!empty($args['product_type'])){
            wp_set_object_terms( $post_id, $args['product_type'], 'product_type' );
        }
        else{
            wp_set_object_terms( $post_id, 'simple', 'product_type' );
        }
        
        // Update the category
        
        $this->dsi_product_update_category($product_id,$args['category']);

        //insert thumbnail
        $thumbnail_id = $this->dsi_set_thumbnail($args['thumbnail']);
        $this->dsi_product_update_meta( $post_id, '_thumbnail_id', $thumbnail_id );

        //insert gallery images
        
        $images_ids = $this->dsi_set_image_gallery($args['images']);
        $images = implode(",",$images_ids);
        $this->dsi_product_update_meta( $post_id, '_product_image_gallery', $images );
            
        $this->dsi_product_update_meta($product_id,'_sku',$args['sku']);
        $this->dsi_product_update_meta($product_id,'_price',$args['price']);
        
        $this->dsi_product_update_meta( $post_id, '_visibility', 'visible' );
        $this->dsi_product_update_meta( $post_id, '_stock_status', 'instock');
        $this->dsi_product_update_meta( $post_id, 'total_sales', '0' );
        $this->dsi_product_update_meta( $post_id, '_downloadable', 'no' );
        $this->dsi_product_update_meta( $post_id, '_virtual', 'no' );
        $this->dsi_product_update_meta( $post_id, '_regular_price', $args['price'] );
        $this->dsi_product_update_meta( $post_id, '_sale_price', '');
        $this->dsi_product_update_meta( $post_id, '_purchase_note', '' );
        $this->dsi_product_update_meta( $post_id, '_featured', 'no' );
        $this->dsi_product_update_meta( $post_id, 'product_shipping_class', '');
        $this->dsi_product_update_meta( $post_id, '_weight', $args['weight'] );
        $this->dsi_product_update_meta( $post_id, '_length', $args['length'] );
        $this->dsi_product_update_meta( $post_id, '_width', $args['width'] );
        $this->dsi_product_update_meta( $post_id, '_height', 0 );
        $this->dsi_product_update_meta( $post_id, '_product_attributes', array() );
        $this->dsi_product_update_meta( $post_id, 'sale_price_dates_from', '' );
        $this->dsi_product_update_meta( $post_id, 'sale_price_dates_to', '' );
        $this->dsi_product_update_meta( $post_id, '_price', $args['price'] );
        $this->dsi_product_update_meta( $post_id, '_sold_individually', '' );
        $this->dsi_product_update_meta( $post_id, '_manage_stock', 'no' );
        $this->dsi_product_update_meta( $post_id, '_backorders', 'no' );
        $this->dsi_product_update_meta( $post_id, '_stock', '' );
    }
    /**
     * Function update Raw SQL
     * 
     * 
     */
    public function update_product_raw_sql($table,$args){
        
        global $wpdb;
        $product_id = $args['id'];
        $this->connect_db();
        //print_r($args);
        
        $post_name = sanitize_title($args['name']);

        $sqlSelectSlug = "
            SELECT * FROM " . $wpdb->prefix. "posts
            WHERE post_name = '" . $post_name . "'
        ";
        
        
        
        $r = $this->con->query($sqlSelectSlug);
        //echo $r->num_rows;
        if($r->num_rows > 0){
            $post_name = $post_name . "-" . ($r->num_rows + 1);
        }

        $sqlUpdate = "
            UPDATE " . $table . " 
            SET 
            post_modified = '" .  date('Y-m-d H:i:s') . "',
            post_modified_gmt= '" .  date('Y-m-d H:i:s') . "',
            post_content = '" .  $args['description'] . "',
            post_title = '" .  $args['name']  . "',
            post_excerpt = '" .  $args['name']  . "',
            post_status = 'publish',
            post_name = '" .  $post_name  . "',
            post_type = 'product',
            to_ping = '',
            pinged = '',
            post_content_filtered = '',
            post_parent = 0,
            post_mime_type = ''

            WHERE

            ID = '" . $product_id . "'
        ";
        //echo $sqlUpdate;
        
        //echo $sqlUpdate;

        $this->con->query($sqlUpdate);

        $post_id = $product_id;
        //Upate the taxonomy
        if(!empty($args['product_type'])){
            wp_set_object_terms( $post_id, $args['product_type'], 'product_type' );
        }
        else{
            wp_set_object_terms( $post_id, 'simple', 'product_type' );
        }    
        
        // Update the category
        
        $this->dsi_product_update_category($product_id,$args['category']);

        //insert thumbnail
        $thumbnail_id = $this->dsi_set_thumbnail($args['thumbnail']);
        $this->dsi_product_update_meta( $post_id, '_thumbnail_id', $thumbnail_id );

        //insert gallery images

        $images = $this->dsi_set_image_gallery($args['images']);
        $images = implode(",",$images);
        
        $this->dsi_product_update_meta( $post_id, '_product_image_gallery', $images );
            
        $this->dsi_product_update_meta($product_id,'_sku',$args['sku']);
        $this->dsi_product_update_meta($product_id,'_price',$args['price']);
        
        $this->dsi_product_update_meta( $post_id, '_visibility', 'visible' );
        $this->dsi_product_update_meta( $post_id, '_stock_status', 'instock');
        $this->dsi_product_update_meta( $post_id, 'total_sales', '0' );
        $this->dsi_product_update_meta( $post_id, '_downloadable', 'no' );
        $this->dsi_product_update_meta( $post_id, '_virtual', 'no' );
        $this->dsi_product_update_meta( $post_id, '_regular_price', $args['price'] );
        $this->dsi_product_update_meta( $post_id, '_sale_price', '');
        $this->dsi_product_update_meta( $post_id, '_purchase_note', '' );
        $this->dsi_product_update_meta( $post_id, '_featured', 'no' );
        $this->dsi_product_update_meta( $post_id, 'product_shipping_class', '');
        $this->dsi_product_update_meta( $post_id, '_weight', $args['weight'] );
        $this->dsi_product_update_meta( $post_id, '_length', $args['length'] );
        $this->dsi_product_update_meta( $post_id, '_width', $args['width'] );
        $this->dsi_product_update_meta( $post_id, '_height', 0 );
        $this->dsi_product_update_meta( $post_id, '_product_attributes', array() );
        $this->dsi_product_update_meta( $post_id, 'sale_price_dates_from', '' );
        $this->dsi_product_update_meta( $post_id, 'sale_price_dates_to', '' );
        $this->dsi_product_update_meta( $post_id, '_price', $args['price'] );
        $this->dsi_product_update_meta( $post_id, '_sold_individually', '' );
        $this->dsi_product_update_meta( $post_id, '_manage_stock', 'no' );
        $this->dsi_product_update_meta( $post_id, '_backorders', 'no' );
        $this->dsi_product_update_meta( $post_id, '_stock', '' );
    }
    /**
     * Function to udpate the meta
     * 
     * @param string $product_id
     * @param string $metakey
     * @param string @metavalue
     * 
     */
    function dsi_product_update_meta($product_id,$metakey,$metavalue){
        global $wpdb;
        $table = $wpdb->prefix . "postmeta";// table prefix
        //check if post_id exist
        // if yes - create new postmeta
        // else - update postmeta with new filter
        $sqlSelectMeta = "
            SELECT * FROM " . $table . " 
            WHERE post_id = '" . $product_id. "'
            AND meta_key = '" . $metakey . "'
            
        ";
        
        $res = $this->con->query($sqlSelectMeta);
        
        if($res->num_rows > 0){
            $sqlUpdateMeta = "
                UPDATE " . $table . " 
                SET meta_value ='" . $metavalue . "'
                WHERE 
                    post_id = '" . $product_id . "'
                AND 
                    meta_key = '" . $metakey . "'
            ";
            $this->con->query($sqlUpdateMeta);
            
        }
        else{
            $sqlInsertMeta = "INSERT INTO " . $table. " (post_id,meta_key,meta_value) VALUES('" . $product_id . "','" . $metakey . "','" . $metavalue . "')";
            $this->con->query($sqlInsertMeta);
        }
    }
    /**
     * Update Product Category
     * 
     */
    function dsi_product_update_category($product_id,$category_id = array()){
        global $wpdb;
        $table = $wpdb->prefix . "term_relationships";// table prefix
        if(!empty($category_id)){
            for($x = 0 ; $x< count($category_id); $x++){
                $sqlInsertTerm = "
                    INSERT INTO " . $table . " (object_id,term_taxonomy_id)VALUES('" . $product_id . "','" . $category_id[$x] ."')
                ";
                $this->con->query($sqlInsertTerm);
                
            }
        }else{
           // wp_set_object_terms( $product_id, 'uncategorized', 'product_cat' );
        }
        
    }
    /** 
     * Manually Set the image
     */
    function dsi_set_thumbnail($thumbnail){
        // SET THUMBNAIL TO POST
        // Add Featured Image to Post
        if(!empty($thumbnail)){
            $image_url        = $thumbnail; // Define the image URL here
            $image_name       = 'main.png';
            $upload_dir       = wp_upload_dir(); // Set upload folder
            $image_data       = file_get_contents($image_url); // Get image data
            $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
            $filename         = basename( $unique_file_name ); // Create image file name

            // Check folder permission and define file location
            if( wp_mkdir_p( $upload_dir['path'] ) ) {
                $file = $upload_dir['path'] . '/' . $filename;
            } else {
                $file = $upload_dir['basedir'] . '/' . $filename;
            }

            // Create the image  file on the server
            file_put_contents( $file, $image_data );

            // Check image file type
            $wp_filetype = wp_check_filetype( $filename, null );

            // Set attachment data
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => sanitize_file_name( $filename ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            // Create the attachment
            $attach_id = wp_insert_attachment( $attachment, $file);

            // Include image.php
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Define attachment metadata
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

            // Assign metadata to attachment
            wp_update_attachment_metadata( $attach_id, $attach_data );

            // And finally assign featured image to post
            
            return $attach_id;
            //set_post_thumbnail( $post_id, $attach_id );
        }
    }
    /**
     * Import and upload image id
     * 
     * @param array $images collection of image URL
     * 
     * @return $image_ids return as array of image Attachment Id
     */
    function dsi_set_image_gallery($images){
        // SET THUMBNAIL TO POST
        // Add Featured Image to Post
        
        $image_ids = array();
        
        for($i = 0; $i < count($images); $i++)
        { 
            
            if(!empty($images[$i])){
                $image_url        = $images[$i]; // Define the image URL here
                $image_name       = 'main.png';
                $upload_dir       = wp_upload_dir(); // Set upload folder
                $image_data       = file_get_contents($image_url); // Get image data
                $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
                $filename         = basename( $unique_file_name ); // Create image file name
    
                // Check folder permission and define file location
                if( wp_mkdir_p( $upload_dir['path'] ) ) {
                    $file = $upload_dir['path'] . '/' . $filename;
                } else {
                    $file = $upload_dir['basedir'] . '/' . $filename;
                }
    
                // Create the image  file on the server
                file_put_contents( $file, $image_data );
    
                // Check image file type
                $wp_filetype = wp_check_filetype( $filename, null );
    
                // Set attachment data
                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title'     => sanitize_file_name( $filename ),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );
    
                // Create the attachment
                $attach_id = wp_insert_attachment( $attachment, $file);
    
                // Include image.php
                require_once(ABSPATH . 'wp-admin/includes/image.php');
    
                // Define attachment metadata
                $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    
                // Assign metadata to attachment
                wp_update_attachment_metadata( $attach_id, $attach_data );
    
                // And finally assign featured image to post
                
                //set_post_thumbnail( $post_id, $attach_id );
                array_push($image_ids,$attach_id);
            }

        }
        /** image Ids for the postmeta */
        return $image_ids;
    }
    /**
     * Get Variation Parent by looking at its sku if the explode('0',is in array of $_POST]['variation_parent_'])
     * 
     * @param string sku 
     * @param string variation_parents_
     * @return string @parent_id
     */
    function process_variation_parent($sku ,$variation_parents,$args=array()){
        $parent_variable_product = '';
        $sku_split = explode('-',$sku);
            if(in_array($sku_split[0],$variation_parents)){
                $parent_variable_product = $sku_split[0]. "-MAIN";
            }
        
        return $parent_variable_product;
    }

    function insert_new_variation_parent($args = array()){
        global $wpdb;
            $this->connect_db();
        //return $args;
        $name = $args['name']. '';
        $sku = $args['sku'];
        $categories = $args['category'];
        $thumbnail1 = $args['thumbnail1'];
        $images = $args['images'];
        $type = 'type';
        
        $args_new = [
            'name'=>$name,
            'type' => $type,
            'sku'=>$sku,
            'price'=>$regular_price,
            'sale_price'=>$sale_price,
            'images'=>$images,
            'thumbnail'=>$thumbnail1,
            'category'=> $categories,
            'length'=>$length,
            'width'=>$width,
            'height'=>$height,
            'weight'=>$weight,
            'product_type' => $product_type
        ];

        //$dsidb = new DSI_Db();

        //check slug
        $post_name = sanitize_title($args_new['name']);

        $sqlSelectSlug = "
            SELECT * FROM " . $wpdb->prefix. "posts
            WHERE post_name = '" . $post_name . "'
        ";

        
        
        $r = $this->con->query($sqlSelectSlug);
        if($r->num_rows > 0){
            $post_name = $post_name . "-" . ($r->num_rows + 1);
        }
        
        // insert to post

        $sqlInsert = "
            INSERT INTO " . $wpdb->prefix."posts
            (
                post_author,
                post_date,
                post_date_gmt,
                post_modified,
                post_modified_gmt,
                post_content,
                post_title,
                post_excerpt,
                post_status,
                post_name,
                post_type,
                to_ping,
                pinged,
                post_content_filtered,
                post_parent,
                post_mime_type

            )
            VALUES
            (
                '" . get_current_user_id(). "', /*post_author*/
                '" .  date('Y-m-d H:i:s') . "',/*post_date*/
                '" .  date('Y-m-d H:i:s') . "', /*post_date_gmt*/
                '" .  date('Y-m-d H:i:s') . "', /*post_modified*/
                '" .  date('Y-m-d H:i:s') . "',/*post_modified_gmt*/
                '" . $args_new['description'] . "',/*post_content*/
                '" . $args['name'] . "', /*post_title*/
                '', /*post_author*/
                'publish', /*post_status*/
                '" . $post_name . "', /*post_name*/
                'product', /*post_type*/
                ' ', /*to_ping*/
                ' ', /*pinged*/
                ' ', /*post_content_filtered*/
                0, /*post_parent*/
                ' ' /*post_mime_type*/
            )
        ";
        $this->con->query($sqlInsert);
        $product_id = $this->con->insert_id;
        $post_id = $product_id;
        //Upate the taxonomy
        
        wp_set_object_terms( $post_id, 'variable', 'product_type' );
        
        
        // Update the category
        
        $this->dsi_product_update_category($product_id,$args_new['category']);

        //insert thumbnail
        $thumbnail_id = $this->dsi_set_thumbnail($args_new['thumbnail']);
        $this->dsi_product_update_meta( $post_id, '_thumbnail_id', $thumbnail_id );

        //insert gallery images

        $images = $this->dsi_set_image_gallery($args_new['images']);
        $images = implode(",",$images);
        $this->dsi_product_update_meta( $post_id, '_product_image_gallery', $images );
            
        $this->dsi_product_update_meta($product_id,'_sku',$args_new['sku']);
        $this->dsi_product_update_meta($product_id,'_price',$args_new['price']);
        
        $this->dsi_product_update_meta( $post_id, '_visibility', 'visible' );
        $this->dsi_product_update_meta( $post_id, '_stock_status', 'instock');
        $this->dsi_product_update_meta( $post_id, 'total_sales', '0' );
        $this->dsi_product_update_meta( $post_id, '_downloadable', 'no' );
        $this->dsi_product_update_meta( $post_id, '_virtual', 'no' );
        $this->dsi_product_update_meta( $post_id, '_regular_price', $args_new['price'] );
        $this->dsi_product_update_meta( $post_id, '_sale_price', '');
        $this->dsi_product_update_meta( $post_id, '_purchase_note', '' );
        $this->dsi_product_update_meta( $post_id, '_featured', 'no' );
        $this->dsi_product_update_meta( $post_id, 'product_shipping_class', '');
        
        $this->dsi_product_update_meta( $post_id, '_product_attributes', array() );
        $this->dsi_product_update_meta( $post_id, 'sale_price_dates_from', '' );
        $this->dsi_product_update_meta( $post_id, 'sale_price_dates_to', '' );
        $this->dsi_product_update_meta( $post_id, '_price', $args_new['price'] );
        $this->dsi_product_update_meta( $post_id, '_sold_individually', '' );
        $this->dsi_product_update_meta( $post_id, '_manage_stock', 'no' );
        $this->dsi_product_update_meta( $post_id, '_backorders', 'no' );
        $this->dsi_product_update_meta( $post_id, '_stock', '' );    
    
                
        //$this->insert_product_raw_sql($table, $args_new);
        return $product_id;
    }

    function get_variation_parent_id($variation_parent_name){
        return false;
    }

    function dsi_create_product_attribute( $label_name ){
        global $wpdb;
        
        $slug = sanitize_title( $label_name );
    
        if ( strlen( $slug ) >= 28 ) {
            return new WP_Error( 'invalid_product_attribute_slug_too_long', sprintf( __( 'Name "%s" is too long (28 characters max). Shorten it, please.', 'woocommerce' ), $slug ), array( 'status' => 400 ) );
        } elseif ( wc_check_if_attribute_name_is_reserved( $slug ) ) {
            return new WP_Error( 'invalid_product_attribute_slug_reserved_name', sprintf( __( 'Name "%s" is not allowed because it is a reserved term. Change it, please.', 'woocommerce' ), $slug ), array( 'status' => 400 ) );
        } elseif ( taxonomy_exists( wc_attribute_taxonomy_name( $label_name ) ) ) {
            return new WP_Error( 'invalid_product_attribute_slug_already_exists', sprintf( __( 'Name "%s" is already in use. Change it, please.', 'woocommerce' ), $label_name ), array( 'status' => 400 ) );
        }
    
        $data = array(
            'attribute_label'   => $label_name,
            'attribute_name'    => $slug,
            'attribute_type'    => 'select',
            'attribute_orderby' => 'menu_order',
            'attribute_public'  => 0, // Enable archives ==> true (or 1)
        );
        // insert woocommerce_attribute_taxonomies
        $results = $wpdb->insert( "{$wpdb->prefix}woocommerce_attribute_taxonomies", $data );
        $id = $wpdb->insert_id;
        // insert wp_termmetas = +order_pa_
        // $data_termmeta = array(
        //     'term_id'=>$id,
        //     'meta_key' => 'order_pa_'. $slug,
        //     'meta_value'=> 0
        // );
        // $t = $GLOBALS['wpdb']->insert( "{$wpdb->prefix}termmeta", $data_termmeta );
        // print_r($t);
        // // insert wp_term_taxonomy +pa
        // $data_term_taxonomy = array(
        //     'term_id'=>$id,
        //     'taxonomy'=>'pa_'. $slug,
        //     'description'=>'',
        //     'parent' => 0,
        //     'count' => 0
        // );
        // $GLOBALS['wpdb']->insert( "{$wpdb->prefix}term_taxonomy", $data_term_taxonomy );
        if ( is_wp_error( $results ) ) {
            return new WP_Error( 'cannot_create_attribute', $results->get_error_message(), array( 'status' => 400 ) );
        }
   
    
        do_action('woocommerce_attribute_added', $id, $data);
    
        wp_schedule_single_event( time(), 'woocommerce_flush_rewrite_rules' );
    
        delete_transient('wc_attribute_taxonomies');
         
        return $id;
    }

    /**
     * Create a product variation for a defined variable product ID.
     *
     * @since 3.0.0
     * @param int   $product_id | Post ID of the product parent variable product.
     * @param array $variation_data | The data to insert in the product.
     */

    
}



// Functions




?>