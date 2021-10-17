<?php 

/**
 * Ajaxl Call for CSV DropShip Imports 
 * Page : page=dropship-import-page
 * Trigger : Button : Upload
 * File : Main View
 * 
 * 
 */
add_action('wp_ajax_upload_csv_files','uploadcsv_files_test');

/** function to ajax upload
 * 
 * Verifying Nonce
 */

function uploadcsv_files(){// the last working
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
        echo json_encode(['message'=>'<h4>Please Select .csv file</h4>']);
        
    }
}

exit();
}

function uploadcsv_files_test(){
    $dir = preg_replace('/wp-content.*$','',__DIR__);
    //echo $dir;
    //header('Content-Type:application/json');
    if(!wp_verify_nonce($_REQUEST['_nonce'],'csv_uploads')){
        echo "ERROR";
    
        
    }
    else{
        $mimesCSV = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');// check ever type of Acceptable CSV Files
        if($_FILES['csv_file'] && in_array($_FILES['csv_file']['type'],$mimesCSV)){
            $csv_file = $_FILES['csv_file']['tmp_name'];
            $wc_fields = array();
            //test_import($csv_file,$wc_fields);
            csv_get_and_send($csv_file,$wc_fields);
        }
        else{
            echo json_encode(['message'=>'<h4>Please Select .csv file</h4>']);
        }
    }
    
    exit();
}


function test_import($csv_file,$wc_fields){
    $prd = new DSI_Products();
    $csv = $_FILES['csv_file']['tmp_name'];// File NAme
    $fo = fopen($csv,'r');// Open the File
    $head = fgetcsv($fo,10000,','); // Read The Heading
    $prd->set_meta_to_import();
    $sample_data = array(
        array('name','10'),
        array('description','16'),
        array('product_type','_product_type'),
        array('visibility','_status'),
        array('total_sales','_total_sales'),
        array('downloadable','_downloadable'),
        array('virtual','_virtual'),
        array('sale_price','_sale_price'),
        array('regular_price','_unit_price'),
        array('featured','_featured'),
        array('weight','_unit_net_weight'),
        array('length','_length'),
        array('width','_width'),
        array('height','_height'),
        array('sku','_product_code'),
        array('product_attributes','EO'),
        array('sale_price_dates_from',''),
        array('sale_price_dtes_to',''),
        array('price','_price'),
        array('backorders','_backorders'),
        array('stock','_stock')
    );

    $lines_of_values = array();
    
    $prd->read_csv_lines($fo);// read the file
    //echo $line;

    
    $line = fgets($fo);
    //echo $line;
    
    $column_assign = $prd->ds_assign_column($sample_data,$head);
    //print_r($column_assign);

    //$prd->import_csv_to_wc($column_assign,$prd->data_per_lines);//
    
    //$prd->dsi_create_products_rest_aw_dropship($prd->data_per_lines);
    //$prd->dsi_create_products_rest_aw_dropship($prd->data_per_lines);
    
    //ar_to_pre($column_assign);
    $objProduct = new WC_Product_Simple();

    
//    json_message($prd->data_per_lines);//
    $prd->dsi_wc_product_simple_bulk_create();

}

//STEPS


function csv_get_and_send($csv_file,$wc_fields){
    header("Content-Type:application/json");
    // READ CSV
    $prd = new DSI_Products();
    $csv = $_FILES['csv_file']['tmp_name'];// File NAme
    $fo = fopen($csv,'r');// Open the File
    $head = fgetcsv($fo,10000,','); // Read The Heading
    $line = fgets($fo);

    $prd->read_csv_lines($fo);

    $sample_data = array(
        array('name','10'),
        array('description','16'),
        array('sku','1'),
        array('description','16'),
        array('price','6'),
        array('weight','12'),
        array('length','16'),
        array('width','16'),
        array('height','16'),
        array('stoc_quantity','16'),
        array('tarrif_code','19'),
    );
    //ar_to_pre($head);
    $upload_mapping = array();
    for($x = 1; $x <= count($sample_data) ; $x++){
        $upload_mapping[$sample_data[$x-1][0]] = $head[$sample_data[$x-1][1]] . "-". $sample_data[$x-1][1];   

    }
    echo json_encode([
            'row'=>$upload_mapping
            ]
        );
    

    
    
    
}
// DISPLAY CSV ON THE FRONT END

//Collect Data Values

//use  DSI_Product->dsi_wc_product_simple()



?>