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
        echo "<div class='alert-primary'>Please Select .csv file</div>";
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
            test_import($csv_file,$wc_fields);
        }
        else{
            echo "<div class='alert-primary'>Please Select .csv file</div>";
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
        array('post_title','_unit_name'),
        array('post_content','_webpage_description_html'),
        array('post_status','_status'),
        array('post_type,','post_type'),
        array('product_type','_product_type'),
        array('_visibility','_status'),
        array('total_sales','_total_sales'),
        array('_downloadable','_downloadable'),
        array('_virtual','_virtual'),
        array('_sale_price','_sale_price'),
        array('_regular_price','_unit_price'),
        array('_featured','_featured'),
        array('_weight','_unit_net_weight'),
        array('_length','_length'),
        array('_width','_width'),
        array('_height','_height'),
        array('_sku','_product_code'),
        array('_product_attributes','EO'),
        array('_sale_price_dates_from',''),
        array('_sale_price_dtes_to',''),
        array('_price','_price'),
        array('_backorders','_backorders'),
        array('_stock','_stock')
    );
<<<<<<< HEAD
    $lines_of_values = array();
    
    $prd->read_csv_lines($fo);// read the file
    //echo $line;
=======
    echo "Hellow";
    $line = fgets($fo);
    echo $line;
    
    ar_to_pre($prd->ds_assign_column($sample_data,$head));
>>>>>>> 927c0a4795209a7fa66b6a89358fdf949772bd65
    
    

    $column_assign = $prd->ds_assign_column($sample_data,$head);
    //print_r($column_assign);
    $prd->import_csv_to_wc($column_assign,$prd->data_per_lines);

}

?>