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
            // test_import($csv_file,$wc_fields);
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

    $prd->read_csv_lines($fo);// read the file
    
    $objProduct = new WC_Product_Simple();

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
    $prd->set_meta_to_import();
    $sample_data = array(
        array('name','10'),
        array('description','16'),
        array('sku','1'),
        array('description','16'),
        array('price','6'),
        array('weight','12'),
        array('length','14'),
        array('width','14'),
        array('height','14'),
        array('stoc_quantity',''),
        array('tarrif_code','19'),
        array('image_1','23'),
        array('image_2','24')
    );
    
    
    $script = "jQuery('#start_import').click(function(e){
        e.preventDefault();
        read_rows_from_table(jQuery(this).siblings('table'));
    }).addClass('button').animate('1000');";
    $objProduct = new WC_Product_Simple();
     
    /** Collected */
    $prd->read_csv_lines($fo);
    $lns = $prd->data_per_lines;

    $data_lines = array();
    for($l = 1; $l < count($lns) ; $l++){
        array_push($data_lines,$lns[$l-1]);
    }
    
    
    //ar_to_pre($data_lines);   
    $upload_mapping = array();  
    for($x = 1; $x <= count($sample_data) ; $x++){
        $csv_value = $prd->data_per_lines[0][$sample_data[$x-1][1]];
        $sample_csv = $prd->data_per_lines[0][$sample_data[$x-1][1]];
        if (strlen($sample_csv) >= 50)
        $sample_csv= substr($sample_csv, 0, 40); //This is a ...script
        else
        $sample_csv = $sample_csv;

        $upload_mapping[$sample_data[$x-1][0]] = $head[$sample_data[$x-1][1]] . "wci_split". $sample_data[$x-1][1] . "wci_split". $sample_csv . "wci_split". $csv_value;   
    }

    echo json_encode([
            'row'=>$upload_mapping,
            'script'=> $script,
            'data_per_lines'=> $data_lines
            ]
        );
}
// DISPLAY CSV ON THE FRONT END

//Collect Data Values

//use  DSI_Product->dsi_wc_product_simple()


/** Receives field names,values, and columns one by one
 * 
 */

add_action('wp_ajax_get_field_then_import','get_field_then_import');
/** Receives field names,values, and columns one by one
 * 
 */

function get_field_then_import(){
    header('Content-Type:application/json');
    
    $prd = new DSI_Products();
    $pid = $prd->dsi_wc_product_simple(
        [
            'name'=>$_POST['lines'][10],
            'sku'=>$_POST['lines'][1],
            'price'=>$_POST['lines'][6],
            'thumbnail'=>$_POST['lines'][23],
            'thumbnail2'=>$_POST['lines'][24],
            'description'=>$_POST['lines'][16]
        ]
    );
    $status_message = 'Ok';
    
    echo json_encode([
        'data'=>[
            'product_id'=> $pid,
            'sku'=> $_POST['lines'][1],
            'name'=> $_POST['lines'][10],
            'price'=> $_POST['lines'][6]
        ],
        'status_message'=>$status_message
    ]);
    exit();
}
?>