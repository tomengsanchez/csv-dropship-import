<?php 
add_action('wp_ajax_nopriv_getgetget',function(){
    print_r($_POST);
    
});

/**
 * Ajaxl Call for CSV DropShip Imports 
 * Page : page=dropship-import-page
 * Trigger : Button : Upload
 * File : Main View
 * 
 * 
 */
add_action('wp_ajax_upload_csv_files','uploadcsv_files');

/** function to ajax upload
 * 
 * Verifying Nonce
 */

function uploadcsv_files(){
    $dir = preg_replace('/wp-content.*$','',__DIR__);
    //echo $dir;
    //header('Content-Type:application/json');
    if(!wp_verify_nonce($_REQUEST['_nonce'],'csv_uploads')){
        echo "ERROR";
    }
    else{
        $mimesCSV = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');// check ever type of Acceptable CSV Files
        if($_FILES['csv_file'] && in_array($_FILES['csv_file']['type'],$mimesCSV)){
            $csv = $_FILES['csv_file']['tmp_name'];
            $fo = fopen($csv,'r');
            $csv_arr = fgetcsv($fo,10000,',');
            echo "<table>";
            for($i = 0; $i <= count($csv_arr);$i++){
                echo "<th>" . $csv_arr[$i] . "</th>";
            }
            
            while(! feof($fo)){
                echo "<tr>";
                $line = fgets($fo);
                $tr = explode(",",$line);
                for($t = 0; $t <= count($tr) ; $t++){
                    echo "<td> " . $tr[$t ]. " </td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        else{
            echo "<div class='alert-primary'>Please Select .csv file</div>";
        }
    }
    
    exit();
}



/**
 * Ajax Call for CSV DropShip Imports 
 * Page : page=dropship-providers 
 * Trigger : Button : Test Product Insert
 * File : DSI_Dropship_Providers_Views
 * 
 * 
 */

add_action('wp_ajax_test_product_import','test_product_import');

function test_product_import(){
    $product = new WC_Product();
    //generate_simple_product();
    ar_to_pre(generate_simple_product());

    exit();

}
/**  */
function generate_simple_product() {
    $name              = 'My Product Name1';
    $will_manage_stock = true;
    $is_virtual        = false;
    $price             = 1000.00;
    $is_on_sale        = true;
    $sale_price        = 999.00;
    $product           = new \WC_Product();
    $image_id = 0; // Attachment ID
    $gallery  = array();
    $product->set_props( array(
        'name'               => $name,
        'featured'           => false,
        'catalog_visibility' => 'visible',
        'description'        => 'My awesome product description',
        'short_description'  => 'My short description',
        'sku'                => sanitize_title( $name ) . '-' . rand(0, 100), // Just an example
        'regular_price'      => $price,
        'sale_price'         => $sale_price,
        'date_on_sale_from'  => '',
        'date_on_sale_to'    => '',
        'total_sales'        => 0,
        'tax_status'         => 'taxable',
        'tax_class'          => '',
        'manage_stock'       => $will_manage_stock,
        'stock_quantity'     => $will_manage_stock ? 100 : null, // Stock quantity or null
        'stock_status'       => 'instock',
        'backorders'         => 'no',
        'sold_individually'  => true,
        'weight'             => $is_virtual ? '' : 15,
        'length'             => $is_virtual ? '' : 15,
        'width'              => $is_virtual ? '' : 15,
        'height'             => $is_virtual ? '' : 15,
        'upsell_ids'         => '',
        'cross_sell_ids'     => '',
        'parent_id'          => 0,
        'reviews_allowed'    => true,
        'purchase_note'      => '',
        'menu_order'         => 10,
        'virtual'            => $is_virtual,
        'downloadable'       => false,
        'category_ids'       => '',
        'tag_ids'            => '',
        'shipping_class_id'  => 0,
        'image_id'           => $image_id,
        'gallery_image_ids'  => $gallery,
    ) );

    $product->save();

    return $product;
}



?>