<?php 

/**
 * Ajaxl Call for CSV DropShip Imports 
 * Page : page=dropship-import-page
 * Trigger : Button : Upload
 * File : Main View
 * 
 * 
 */

use Automattic\Jetpack\Constants;

use function Composer\Autoload\includeFile;



add_action('wp_ajax_upload_csv_files','uploadcsv_files_test');

/** function to ajax upload
 * 
 * Verifying Nonce
 */




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
            if($_POST['dropship_company'] == 'aw-dropship'){
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
                    array('image_1','23'),
                    array('image_2','24'),
                    array('Family','3'),
                    array('Category','31'),
                    
                );
                csv_get_and_send($csv_file,$wc_fields,$sample_data);
            }

            
            if($_POST['dropship_company'] == 'idropship'){
                $sample_data = array(
                    array('name','3'),
                    array('type','1'),
                    array('description','16'),
                    array('Category','26'),
                    array('sku','2'),
                    array('description','8'),
                    array('regular_price','25'),
                    array('sale_price','24'),
                    array('item_group','36'),
                    array('item_subgroup','37'),
                    array('brand','34'),
                    array('weight','12'),
                    array('length','14'),
                    array('width','14'),
                    array('height','14'),
                    array('image','27'),
                    
                    
                );
                get_csv_and_send_idropship($csv_file,$wc_fields,$sample_data);
            }
            
        }
        else{
            echo json_encode(['message'=>'<h4 span="padding-left:20px">Please Select .csv file</h4>']);
        }
    }
    
    exit();
}

function get_csv_and_send_idropship($csv_file,$wc_fields,$sample_data){
    header("Content-Type:application/json");
    $prd = new DSI_Products();
    $csv = $_FILES['csv_file']['tmp_name'];// File NAme
    $fo = fopen($csv,'r');// Open the File
    $head = fgetcsv($fo,10000,','); // Read The Heading
    $prd->set_meta_to_import();
  
    // Variables
    
    $cats= array();
    $name = '';
    $desciption = '';
    $sku = '';
    $price= 0;
    $weight = 0;
    $length = '';
    $width = '';
    $height = '';

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
        
        if($sample_data[$x-1][0] == 'description'){
            //$sample_csv = 'Webpage Description (html)';
            $desciption = $sample_data[$x-1][0];
            $sample_csv = substr($sample_csv,0,50) . "......";
        }
        if($sample_data[$x-1][0] == 'name'){
            $name = $sample_data[$x-1][0];
            $name_column = $sample_data[$x-1][1];
            $name_heading = $head[$name_column];
        }
        if($sample_data[$x-1][0] == 'name'){
            $desciption = $sample_data[$x-1][0];
            
        }
        
        
        $upload_mapping[$sample_data[$x-1][0]] = $head[$sample_data[$x-1][1]] . "wci_split". $sample_data[$x-1][1] . "wci_split". $sample_csv . "wci_split". $csv_value;   
    }

    //REsults
    for($c = 1; $c < count($prd->data_per_lines); $c++){
        array_push($cats,$prd->data_per_lines[$c-1][3]);
    }
    $cats = array_unique($cats);
    
    if(!in_array($name_heading, $prd->valid_name_heading)){
        $data_lines = array();
    }
    $script = '';
    echo json_encode([
        'row'=>$upload_mapping,
        'script'=> $script,
        'data_per_lines'=> $data_lines,
        'categories'=>$cats,
        'valid' => in_array($name_heading,$prd->valid_name_heading)
        ]
    );
    exit();
}


//STEPS


function csv_get_and_send($csv_file,$wc_fields,$sample_data){
    header("Content-Type:application/json");
    // READ CSV

    
    $prd = new DSI_Products();
    $csv = $_FILES['csv_file']['tmp_name'];// File NAme
    $fo = fopen($csv,'r');// Open the File
    $head = fgetcsv($fo,10000,','); // Read The Heading
    $prd->set_meta_to_import();
    
    $objProduct = new WC_Product_Simple();
     
    /** Collected */
    $prd->read_csv_lines($fo);
    $lns = $prd->data_per_lines;

    $data_lines = array();
    for($l = 1; $l < count($lns) ; $l++){
        array_push($data_lines,$lns[$l-1]);
    }
    
    
    //ar_to_pre($data_lines);   
    $cats= array();
    $upload_mapping = array();  
    for($x = 1; $x <= count($sample_data) ; $x++){
        $csv_value = $prd->data_per_lines[0][$sample_data[$x-1][1]];
        $sample_csv = $prd->data_per_lines[0][$sample_data[$x-1][1]];
      
        if($sample_data[$x-1][0] == 'description'){
            //$sample_csv = 'Webpage Description (html)';
            $sample_csv = substr($sample_csv,0,50) . "......";
        }
        if($sample_data[$x-1][0] == 'name'){
            $name = $sample_data[$x-1][0];
            $name_column = $sample_data[$x-1][1];
            $name_heading = $head[$name_column];
        }
        
        $upload_mapping[$sample_data[$x-1][0]] = $head[$sample_data[$x-1][1]] . "wci_split". $sample_data[$x-1][1] . "wci_split". $sample_csv . "wci_split". $csv_value;   
    }
    //get Categories
    for($c = 1; $c < count($prd->data_per_lines); $c++){
        array_push($cats,$prd->data_per_lines[$c-1][3]);
    }
    $cats = array_unique($cats);

    
        
    $script = '';

    echo json_encode([
        'row'=>$upload_mapping,
        'script'=> $script,
        'data_per_lines'=> $data_lines,
        'categories'=>$cats,
        'valid' => in_array($name_heading,$prd->valid_name_heading)
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

    //instatiate WC_PRODUCTs
    $prod_inst = new WC_Product();
    $price = $_POST['lines'][6];

    if($_POST['upload_images_yes'] == 'true'){
        $thumbnail1 = $_POST['lines'][23];
        $thumbnail2 = $_POST['lines'][24];
    }
    else{
        $thumbnail1 = null;
        $thumbnail2 = null;
    }
    

    $dim = $_POST['lines'][14];
    $dim = trim($dim,'(mm)');
    $dim = explode('x',$dim);
    $lnt = $dim[0];
    $wdt = $dim[1];
    $sku = $_POST['lines'][1];

    $prd = new DSI_Products();
    
    $update_id = '';
    $products = wc_get_products( array(
        'sku'=>$sku
    ) );
    $existing = 0;
    foreach($products as $p){
        $update_id = $p->get_id();
        $existing = 1;
    }
    $status_message = '';

    //Price 
    $percent = $_POST['mark_up_value']/100;
    if($_POST['mark_up_base'] == 'None'){
        $price = $price;
    }
    else if($_POST['mark_up_base'] == 'Price $'){
        $price = $price + $_POST['mark_up_value'];
    }
    else if($_POST['mark_up_base'] == 'Percentage %'){
        $price = $price + ($price * $percent);
    }

    //round of the $price
    $price = number_format($price,2);

    $cats = $_POST['category'];//all cats in the file

    $categ_col = $_POST['selected_category'];

    $category_id = $prd->category_manipulation($_POST['lines'][$categ_col]);
    

    //$category_id = get_product_category_id($_POST['lines'][3]); //cats per line
    $image_name1  = $_POST['lines'][23];
    
    if($existing == 1){ // IF SKU IS 
        if($_POST['skip_existing_sku_yes']=='false'){
            if($_POST['upload_images_yes'] == 'true'){
                //delete_ main image
                $p = new WC_Product($update_id);

                $attachmentID= $p->get_image_id();


                //wp_delete_attachment('34041', true);

                $attachment_path = get_attached_file( $attachmentID); 
                //Delete attachment from database only, not file
                $delete_attachment = wp_delete_attachment($attachmentID, true);
                //Delete attachment file from disk
                $delete_file = unlink($attachment_path);

                //delete all gallery images


                $gallery_image_ids= $p->get_gallery_image_ids();

                for($i = 0; $i <= count($gallery_image_ids) ; $i++){

                    $attachment_path = get_attached_file( $gallery_image_ids[$i]); 
                    //Delete attachment from database only, not file
                    $delete_attachment = wp_delete_attachment($gallery_image_ids[$i], true);
                    //Delete attachment file from disk
                    $delete_file = unlink($attachment_path);
                }
                
            }
            

            $pid = $prd->dsi_wc_product_simple_update([
                'id'=> $update_id,
                'name'=>$_POST['lines'][10],
                'sku'=>$sku,
                'price'=>$price,
                'thumbnail'=>$thumbnail1,
                'thumbnail2'=>$thumbnail2,
                'description'=>$_POST['lines'][16],
                'category'=> $category_id,
                'length'=>$lnt,
                'width'=>$wdt,
                'height'=>'0',
                'weight'=>$_POST['lines'][12],

            ]);
        
            $status_message = 'Updated';
        }
        else{
            $status_message = 'Skipped';
        }
    }
    else{
        $pid = $prd->dsi_wc_product_simple(
            [
                'name'=>$_POST['lines'][10],
                'sku'=>$sku,
                'price'=>$price,
                'thumbnail'=>$thumbnail1,
                'thumbnail2'=>$thumbnail2,
                'description'=>$_POST['lines'][16],
                'category'=> $category_id,
                'length'=>$lnt,
                'width'=>$wdt,
                'height'=>'0',
                'weight'=>$_POST['lines'][12],
            ]
        );
        $status_message = 'Created';
    }



    //Category Manipulation : Add/EDIT product category on first row only
    echo json_encode([
        'data'=>[
            'product_id'=> $pid,
            'sku'=> $_POST['lines'][1],
            'name'=> $_POST['lines'][10],
            'price'=> $price,
            'category'=>$_POST['lines'][$_POST['selected_category']],
            'length'=> $dim[0],
            'width'=> $dim[1].
            ''
        ],
        'status_message'=>$status_message,
        'selected_cat' => $_POST['selected_category'],
        'mark_up_base' => $_POST['mark_up_base'],
        'mark_up_value' => $_POST['mark_up_value'],
        'mark_up_perc' => $percent,
        'price_up'=> $price,
        'media' => get_attached_media( '', $pid ) 

    ]);
    exit();
}


function get_product_category_id($cat_name){
    $cat_args = array(
        'hide_empty' => false,
        'name'=>$cat_name
    );
    $res = get_terms('product_cat',$cat_args);
    return $res[0]->term_id;

}

add_action('wp_ajax_add_ajax_script','add_ajax_script');

function add_ajax_script(){
    
    ?>
    <script >
        <?php insert_js_locally('main.js'); ?>
    </script> 
        <button class='tomengbutton'>test Jav</button>
    <?php 
    
    exit();
}

/**
 * Function that insert javascript inside an ajax
 * 
 * @param file $jsFile js file inside the ajaxes/jScript/ folder
 */
function global_ajax_script($jsFile){

    $ajax_url = plugin_dir_url('12') . "csv-dropship-import/ajaxes/jScript/" .$jsFile;
    
    return $ajax_url;
    //return "<script src='". $ajax_url ."'></script>";
    
    exit();
}
function insert_js_locally($jsFile){
    $ajax_url = plugin_dir_path(__FILE__) . "/jScript/" . $jsFile;
    require_once($ajax_url);
}

add_action('wp_ajax_get_ajax_script_main_page','get_ajax_script_main_page');
/** 
 * Ajax result for javascript inside an ajax
 */
function get_ajax_script_main_page(){
    ?>
    <script>
        jQuery(document).ready(function(){
            jQuery('#start_import').click(function(e){
                e.preventDefault();
                //selected_category_column = jQuery(this).parent().siblings('td.td_select').children('select.select_category_column').val();
                mark_up_price_base = jQuery('select.price_mark_up_select').val();
                mark_up_price_base = jQuery('select.price_mark_up_select').val();
                mark_up_price_value = jQuery('input#price_mark_up_text').val();
                upload_images_c = jQuery('input#upload_images').is(':checked');
                
                skip_existing_sku_c = jQuery("#skip_existing_sku").is(':checked');
                sel = jQuery('select.select_category_column').val();
                if(jQuery('select.price_mark_up_select').val()=='None'){
                    if(jQuery('#dropship_company').val()== 'aw-dropship'){
                        read_rows_from_table(jQuery(this).siblings('table'),sel,mark_up_price_base,mark_up_price_value,upload_images_c,skip_existing_sku_c);    
                    }
                    else{
                        read_rows_from_table(jQuery(this).siblings('table'),sel,mark_up_price_base,mark_up_price_value,upload_images_c,skip_existing_sku_c);    
                    }
                    // jQuery(this).attr('disabled','disabled');
                    // jQuery('select.price_mark_up_select').attr('disabled','disabled');
                    // jQuery('input#price_mark_up_text').attr('disabled','disabled');
                    // jQuery('input#upload_images').attr('disabled','disabled');
                    // jQuery("#skip_existing_sku").attr('disabled','disabled');
                    // jQuery('select.select_category_column').attr('disabled','disabled');
                }
                else{
                    if(mark_up_price_value ==''){
                        alert('Please Input Number');
                        jQuery('input#price_mark_up_text').focus();
                    }
                    else{
                        if(validate_number(jQuery('input#price_mark_up_text'))){
                            if(jQuery('#dropship_company').val()== 'aw-dropship'){
                                read_rows_from_table(jQuery(this).siblings('table'),sel,mark_up_price_base,mark_up_price_value,upload_images_c,skip_existing_sku_c);    
                            }
                            else{
                                read_rows_from_table(jQuery(this).siblings('table'),sel,mark_up_price_base,mark_up_price_value,upload_images_c,skip_existing_sku_c);    
                            }

                            // jQuery(this).attr('disabled','disabled');
                            // jQuery('select.price_mark_up_select').attr('disabled','disabled');
                            // jQuery('input#price_mark_up_text').attr('disabled','disabled');
                            // jQuery('input#upload_images').attr('disabled','disabled');
                            // jQuery("#skip_existing_sku").attr('disabled','disabled');
                            // jQuery('select.select_category_column').attr('disabled','disabled');
                        }
                        else{
                            alert('Mark Up Price is not a Number');
                            jQuery('input#price_mark_up_text').focus();
                        }
                    }
                }

                
                
            }).addClass('button').animate('10000');
            
            jQuery('select.price_mark_up_select').change(function(){
                //console.log(jQuery(this).val());
                if(jQuery(this).val() == 'None'){
                    jQuery('input#price_mark_up_text').attr('disabled','disabled');
                }
                else{
                    jQuery('input#price_mark_up_text').removeAttr('disabled');
                }
            });
        });
    </script>
    <?php 
    exit();
}

add_action('wp_ajax_get_field_then_import_idropship',function(){
    header('Content-Type:application/json');
    $name = $_POST['lines'][3];
    $description = $_POST['lines'][8];
    $sku = $_POST['lines'][2];
    $regular_price = $_POST['lines'][25];
    $sale_price = $_POST['lines'][24];
    $brand = $_POST['lines'][3];
    $weight = '';
    $length = '';
    $width = '';
    $height = '';
    $image = '';
    $category = '';
    




    echo json_encode([
        'data'=>[
            'product_id'=> $pid,
            'sku'=> $_POST['lines'][1],
            'name'=> $_POST['lines'][3],
            'price'=> $price,
            'category'=>$_POST['lines'][$_POST['selected_category']],
            'length'=> $dim[0],
            'width'=> $dim[1].
            ''
        ],
        'status_message'=>$status_message,
        'selected_cat' => $_POST['selected_category'],
        'mark_up_base' => $_POST['mark_up_base'],
        'mark_up_value' => $_POST['mark_up_value'],
        'mark_up_perc' => $percent,
        'price_up'=> $price,
        'media' => get_attached_media( '', $pid ) 

    ]);
    exit();
});
?>

