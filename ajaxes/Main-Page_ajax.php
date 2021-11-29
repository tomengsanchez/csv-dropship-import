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
                    array('stock','14'),
                    array('item_subgroup','37'),
                    array('brand','34'),
                    array('weight','18'),
                    array('length','19'),
                    array('width','20'),
                    array('height','21'),
                    array('image','29'),
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


    $remove_this_attrib = [
        'Pcs',
        'kg',
        'pcs'
    ];

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

    $variation_parents = array();
    $data_lines = array();
    $transient_lines = [];
    for($l = 1; $l < count($lns) ; $l++){
        array_push($data_lines,$lns[$l-1]);
        array_push($variation_parents,$lns[$l-1][3] ."wci_split".$lns[$l-1][2]);
        $transient_lines[] = $data_lines;
    }
    set_transient('dsi_trans_idropship',$data_lines);
    //ar_to_pre($data_lines);

    //Play with Collected SKU then convert them into    \
    $variation_parents_title = array();
    $variation_parents_processed = array();
    $variations = array();
    foreach($variation_parents as $vp){
        $sku_split = explode('-',$vp);
        if(count($sku_split) > 1){
            array_push($variations,$vp);
            array_push($variation_parents_processed,explode('wci_split',$sku_split[0])[1]);
            array_push($variation_parents_title,explode('wci_split',$sku_split[0])[0]);
        }
    }
    $variation_parents_processed = array_unique($variation_parents_processed);
    $variation_parents_processed_with_final_parent_name = array();
    $variation_parents_processed_with_title_names = array();

   
    foreach($variation_parents_processed as $vpp){
        $title = '';
        foreach($variations as $v){
            $vexp = explode('wci_split',$v);
            $variation_titles = $vexp[0];
            $variation_sku_parent = explode('-',$vexp[1])[0];
            if($vpp == $variation_sku_parent){
                $title .= $variation_titles . "wci_split";
            }
        }
        //play with the functions
        $title = rtrim($title,'wci_split');
        $title_exp = explode('wci_split',$title);

        $names = $title_exp;
        //ar_to_pre($names);
        $sliced_word = array();    
        foreach($names as $n){
            $namesexp = explode(' ',$n);
            array_push($sliced_word, $namesexp);
        }
        
        $collected_words = array();
        
        $occurence_counter = 0;
        $collected_words_with_counter = array();

        foreach($sliced_word as $sw){
            for($s = 0; $s < count($sw);$s++){
                array_push($collected_words,current($sw). "-" . $s);
                $collected_words_with_counter[current($sw). "-" . $s] = 0;
                next($sw);
            }
            
        }


        //loop collected words.
        foreach($collected_words as $cw){
            //check if each collected words are in the keys of collected words with counter.
            foreach($collected_words_with_counter as $key => $val){
                if($key == $cw){
                    $val = $val+1;
                    $collected_words_with_counter[$key] = $val;
                }
            }
        }
        //ksort($collected_words_with_counter);
        //ar_to_pre($collected_words_with_counter);
        $arrange_word = array();
        $arrange_index = array();
        //delete words that do not appear often
        for($x = round(count($names)); $x<= count($names) ; $x++){
            foreach($collected_words_with_counter as $ky => $vl){
                if($vl == $x){
                    //echo $ky . "-" . $vl . "<br>";
                    array_push($arrange_index, $ky);            
                }
            }
        }
        //ar_to_pre($arrange_index);
        $new_ar = array();
        $ptitle = '';
        for($s = 0; $s < count($arrange_index); $s++){
            for($b = 0; $b <= count($arrange_index); $b++){
                if(explode('-',$arrange_index[$s])[1] == $b){
                    $new_ar[explode('-',$arrange_index[$s])[1]] = explode('-',$arrange_index[$s])[0];
                }
            }
        }
        
        ksort($new_ar);
        foreach($new_ar as $nval){
            $nval = str_replace(' ', '', $nval);
            $ptitle .= $nval. " ";
        }
        $parent_title = rtrim($ptitle);//Final
        $title = $parent_title;

        $variation_parents_processed_with_final_parent_name[$vpp] = $title;
    }
    
    $variation_attributes_terms = array();
    $xve = 0;
    foreach($variation_parents_processed_with_final_parent_name as $key => $val){
        $attr_string = '';
        foreach($variations as $v){
            $vexp = explode('wci_split',$v);    
            $get_key = explode('-',$vexp[1]);
            if($get_key[0] == $key){
                $vexpst = $vexp[0];
                $vexpst = rtrim($vexpst,' ');
                $title_exp = explode($val,$vexpst);
                $title_form = implode(" ",$title_exp);
                $sku_trim = rtrim($vexp[1]);

                $title_form = ltrim($title_form);
                
                $attr_string .= $sku_trim ." " . $title_form . "|";

                // $val_exp = explode($val,$vexp[0]);
                // foreach($val_exp as $valxp){
                //     $sku_trim = rtrim($vexp[1]);
                //     $att = ltrim($valxp);
                //     if($valxp != ''){
                //         $attr_string.=  $sku_trim ." " . $att . "|";
                //     }
                    
                // }
            }
        }
        
        $attr_string = rtrim($attr_string,'|');
        $attr_string = ltrim($attr_string,'|');
        $variation_attributes_terms[$key] = $attr_string;
        
    }
    
    //make variation parents unique
    
    $upload_mapping = array();  
    //if(!get_transient('dsi_trans_data_lines_idropship')){
        //set_transient('dsi_trans_data_lines_idropship',$data_lines,0);
    //}
    for($x = 1; $x <= count($sample_data) ; $x++){
        $csv_value = $prd->data_per_lines[0][$sample_data[$x-1][1]];
        $sample_csv = $prd->data_per_lines[0][$sample_data[$x-1][1]];
        if($sample_data[$x-1][0] == 'description' || $sample_data[$x-1][0] == 'image'){
            //$sample_csv = 'Webpage Description (html)';
            $desciption = $sample_data[$x-1][0];
            $sample_csv = substr($sample_csv,0,50) . "...";
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
        'valid' => in_array($name_heading,$prd->valid_name_heading),
        'variations'=>$variations,
        'variation_parents' => $variation_parents_processed,
        'variation_parents_title'=>$variation_parents_title,
        'variation_parent_with_title'=>$variation_parents_processed_with_final_parent_name,
        'variation_attribute_terms'=>$variation_attributes_terms
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
        $sample_csv = $prd->data_per_lines[0][$sample_data[$x-1][1]] . "";
      
        if($sample_data[$x-1][0] == 'description'){
            //$sample_csv = 'Webpage Description (html)';
            $sample_csv = substr($sample_csv,0,50) . "...";
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
    $starttime = microtime(true);

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

            $images_array = array(
                $thumbnail2
            );
            
            $categories = array(
                $category_id
            );
            $args =[
                'id'=> $update_id,
                'name'=>$_POST['lines'][10],
                'sku'=>$sku,
                'price'=>$price,
                'thumbnail'=>$thumbnail1,
                'images'=> $images_array,
                'description'=>$_POST['lines'][16],
                'category'=> $categories,
                'length'=>$lnt,
                'width'=>$wdt,
                'height'=>'0',
                'weight'=>$_POST['lines'][12],
                'product_type', 'simple'
            ];

            global $wpdb;
            $table = $wpdb->prefix. "posts";
            $prd->update_product_raw_sql($table,$args);
            $status_message = 'Updated';
        }
        else{
            $status_message = 'Skipped';
        }
    }
    else{

        // $args = 
        // [
        //     'name'=>$_POST['lines'][10],
        //     'sku'=>$sku,
        //     'price'=>$price,
        //     'thumbnail'=>$thumbnail1,
        //     'thumbnail2'=>$thumbnail2,
        //     'description'=>$_POST['lines'][16],
        //     'category'=> $category_id,
        //     'length'=>$lnt,
        //     'width'=>$wdt,
        //     'height'=>'0',
        //     'weight'=>$_POST['lines'][12],
        // ];
        
        // $pid = $prd->dsi_wc_product_simple($args);

        $images_array = array(
            $thumbnail2
        );

        $categories = array(
            $category_id
        );
        $args = 
        [
            'name'=>$_POST['lines'][10],
            'sku'=>$sku,
            'price'=>$price,
            'thumbnail'=>$thumbnail1,
            'images'=>$images_array,
            'description'=>$_POST['lines'][16],
            'category'=> $categories,
            'length'=>$lnt,
            'width'=>$wdt,
            'height'=>'0',
            'weight'=>$_POST['lines'][12],
            'product_type', 'simple'
        ];
        global $wpdb;
        $table = $wpdb->prefix . "posts";
        
         $pid = $prd->insert_product_raw_sql($table,$args);
        
        $status_message = 'Created';
    }



    //Category Manipulation : Add/EDIT product category on first row only
    $endtime = microtime(true);
    $loading_time = ($endtime - $starttime);
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
        'media' => get_attached_media( '', $pid ) ,
        'loading_time' => number_format($loading_time,2)
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
    exit();
    ?>
    <script >
        <?php insert_js_locally('main.js'); ?>
    </script> 
        <button class='tomengbutton'>test Jav</button>
    <?php 
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
    exit();
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
                    jQuery(this).attr('disabled','disabled');
                    jQuery('select.price_mark_up_select').attr('disabled','disabled');
                    jQuery('input#price_mark_up_text').attr('disabled','disabled');
                    jQuery('input#upload_images').attr('disabled','disabled');
                    jQuery("#skip_existing_sku").attr('disabled','disabled');
                    jQuery('select.select_category_column').attr('disabled','disabled');
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

                            jQuery(this).attr('disabled','disabled');
                            jQuery('select.price_mark_up_select').attr('disabled','disabled');
                            jQuery('input#price_mark_up_text').attr('disabled','disabled');
                            jQuery('input#upload_images').attr('disabled','disabled');
                            jQuery("#skip_existing_sku").attr('disabled','disabled');
                            jQuery('select.select_category_column').attr('disabled','disabled');
                        }
                        else{
                            alert('Mark Up Price is not a Number');
                            jQuery('input#price_mark_up_text').focus();
                        }
                    }
                }
                x =0;
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
/**
 * 
 */

add_action('wp_ajax_get_field_then_import_idropship','get_field_then_import_idropship');

function get_field_then_import_idropship(){
    global $wpdb;
    header('Content-Type:application/json');
    // $_POST['lines'] = get_transient('dsi_trans_idropship');
    // $_POST['lines'] = $_POST['lines'][$_POST['line_counter_']];
    $product_id = '';
    $name = $_POST['lines'][3];
    $type = $_POST['lines'][1];
    $description = $_POST['lines'][8];
    $sku = $_POST['lines'][2];
    $status = $_POST['lines'][4];
    $regular_price = $_POST['lines'][25];
    $sale_price = $_POST['lines'][24];
    $brand = $_POST['lines'][34];
    $weight = $_POST['lines'][18];
    $length = $_POST['lines'][19];
    $width = $_POST['lines'][20];
    $height = $_POST['lines'][21];
    $image = $_POST['lines'][29];
    $stock = $_POST['lines'][14];
    $category = $_POST['lines'][26];
    $thumbnail1 = '';
    $action = '';
    $product_type = 'simple';
    $images = array();
    $image_array = explode(',',$image);
    $xc = 0;                                                   
    if($_POST['upload_images_yes'] == 'true'){
        foreach($image_array as $im){
            array_push($images,$im);
        $xc++;
        }
    }
    else{
        // $thumbnail1 = null;
        // $thumbnail2 = null;
    }
    
    $prd = new DSI_Products();
    $update_id = '';
    $categories = $prd->category_manipulation_nested($category);
    
    //product type
    $sku_0 = explode('-',$sku)[0];
    $variation_parent = '';
    $variation_attributes = '';
    
    if(in_array($sku_0, $_POST['variation_parents_'])){
        $product_type = 'variable';
        $variation_parent = $sku_0;
        $variation_parent_title = $_POST['variation_parents_with_title_'][$variation_parent];
        $name_exp = array();
        $name_exp = explode($variation_parent_title,$name);

        $variation_attributes = $name_exp;
        $variation_attributes = implode('',$name_exp);
        $variation_attributes = ltrim($variation_attributes);
        $variation_attributes = trim($variation_attributes);
    }
    
    // $variation_parents_id = '';

    // $attrib_id= $GLOBALS['wpdb']->get_results("SELECT * FROM " . $wpdb->prefix ."woocommerce_attribute_taxonomies WHERE attribute_label ='Variations' ");
    
    // $parent_attribute_id = '';
    // if(count($attrib_id) ==0 ){
    //     //echo "Create Attributes-";
    //     $parent_attribute_id =  $prd->dsi_create_product_attribute('Variations');
    // }else{
    //     $parent_attribute_id = $attrib_id[0]->attribute_id;
    // }
    $q_get_existing = 'SELECT * FROM ' . $GLOBALS['wpdb']->prefix . 'postmeta WHERE meta_value="' . $sku . '" AND meta_key ="_sku"';
    $product_existing = $GLOBALS['wpdb']->get_results($q_get_existing);
    $existing = 0;
    if(count($product_existing) == 1){
        $existing = 1;
    }

    $variation_parents_sku = $prd->process_variation_parent($sku,$_POST['variation_parents_'],$_POST['lines']);
    $variation_parents_loop = wc_get_products([
        'sku'=>$variation_parents_sku
    ]);

    if($product_type == 'variable' ){
    
        //echo $variation_parents_id;
        
        if($variation_parents_loop == null){
            
            if($existing == 1){
                if($_POST['skip_existing_sku_yes']=='false'){
                    $q = 'SELECT * FROM ' . $GLOBALS['wpdb']->prefix . 'postmeta WHERE meta_value="'  . $variation_parents_sku  . '"';
                    $d = $GLOBALS['wpdb']->get_results($q);
                    $variation_parents_id = $d[0]->post_id;
                    $product = new WC_Product_Variable($variation_parents_id);        
                    $parent_args = [
                        'name'=>$variation_parent_title,
                        'sku'=>$variation_parents_sku,
                        'category'=>$categories
                    ];
                    
                    $product = new WC_Product_Variable($variation_parents_id);
                    $product->set_name($variation_parent_title);
                    $product->set_sku($variation_parents_sku);
                    $thumb_id = $prd->dsi_set_thumbnail($images[0]);
                    next($images);
                    $product->set_image_id($thumb_id);
                    $imgs_id = $prd->dsi_set_image_gallery($images);
                    $imgs_id_imp = implode(',', $imgs_id);
                    $product->set_gallery_image_ids($imgs_id_imp);
                    $attribute = new WC_Product_Attribute();
                    $attribute->set_id(0);
                    $attribute->set_name('variants');
                    $vp_sku = rtrim($variation_parents_sku,'-MAIN');
                    $attribute->set_options(explode(WC_DELIMITER, $_POST['variation_attribute_terms_'][$vp_sku]));
                    
                    $product->set_category_ids($categories);
                    $attribute->set_visible(true);
                    $attribute->set_variation(true);
                    
                    $product->set_attributes(array($attribute));
                    
                    $variation_parents_id=$product->save();
                }
            }
            else{
                /** INSERT SCRIPT */
                $parent_args = [
                    'name'=>$variation_parent_title,
                    'sku'=>$variation_parents_sku,
                    'category'=>$categories
                ];
                //$variation_parents_id = $prd->insert_new_variation_parent($parent_args);

                $product = new WC_Product_Variable($variation_parents_id);
                $thumb_id = $prd->dsi_set_thumbnail($images[0]);
                next($images);
                $product->set_image_id($thumb_id);
                $imgs_id = $prd->dsi_set_image_gallery($images);
                $imgs_id_imp = implode(',', $imgs_id);
                $product->set_gallery_image_ids($imgs_id_imp);
                $attribute = new WC_Product_Attribute();
                $attribute->set_id(0);
                $attribute->set_name('variants');
                $vp_sku = rtrim($variation_parents_sku,'-MAIN');
                $attribute->set_options(explode(WC_DELIMITER, $_POST['variation_attribute_terms_'][$vp_sku]));
                
                $product->set_category_ids($categories);
                $attribute->set_visible(true);
                $attribute->set_variation(true);
                
                $product->set_attributes(array($attribute));
                
                $variation_parents_id=$product->save();
            }
            

            $product = new WC_Product_Variable($variation_parents_id);
            if($existing == 1){
                if($_POST['skip_existing_sku_yes']=='false'){
                    $product->set_name($variation_parent_title);
                    $product->set_sku($variation_parents_sku);
                }
            }
            
            $teststring = 'Insert Parent';
        }
        else{
            $q = 'SELECT * FROM ' . $GLOBALS['wpdb']->prefix . 'postmeta WHERE meta_value="'  . $variation_parents_sku  . '"';
            $d = $GLOBALS['wpdb']->get_results($q);
            $variation_parents_id = $d[0]->post_id; 
            $teststring = 'Update Parent';
        }
    }
    
    
    // // work with categories

    // //$images = array();
    $variants_child_sku = $sku;

    // $xc = 0;
    
    if($existing == 1){ // IF SKU IS 
        $action = 'update';
        //exit();
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

            }
            $args = [
                'id'=> $update_id,
                'name'=>$name,
                'sku'=>$sku,
                'price'=>$regular_price,
                'sale_price'=>$sale_price,
                'images'=>$images,
                'thumbnail'=> $thumbnail1,
                'description'=>$description,
                'category'=> $categories,
                'length'=>$length,
                'width'=>$length,
                'height'=>'0',
                'weight'=>$weight,
                'product_type' => $product_type

            ];
            global $wpdb;

            $table = $wpdb->prefix. "posts";
            if($product_type=='simple'){
                //$prd->update_product_raw_sql($table,$args);
            }
            else{
                //$prd->update_product_raw_sql($table,$args);
            }
            

            $status_message = 'Updated';
        }
        else{
            $status_message = 'Skipped';
        }
    }
    else{
        /** INSERT SCRIPT */
        $action = 'insert';
        
        $thumbnail1 = $images[0];
        
        // $args = [
        //     'name'=>$name,
        //     'type' => $type,
        //     'sku'=>$sku,
        //     'post_parent'=>$variation_parents_id,
        //     'price'=>$regular_price,
        //     'sale_price'=>$sale_price,
        //     'images'=>$images,
        //     'description'=>$description,
        //     'thumbnail'=>$thumbnail1,
        //     'category'=> $categories,
        //     'length'=>$length,
        //     'width'=>$width,
        //     'height'=>$height,
        //     'weight'=>$weight,
        //     'product_type' => $product_type
        // ];

        // global $wpdb;
        // $table = $wpdb->prefix . "posts";
        
        if($product_type == 'simple'){
            $simple = new WC_Product_Simple();
            $simple->set_name($name);
            //$variation->set_parent_id($variation_parents_id);

            $thumb_id = $prd->dsi_set_thumbnail($images[0]);
            $simple->set_image_id($thumb_id);
            $imgs_id = $prd->dsi_set_image_gallery($images);
            $imgs_id_imp = implode(',', $imgs_id);
            $simple->set_gallery_image_ids($imgs_id_imp);
            $simple->set_regular_price($regular_price);
            $simple->set_sale_price($sale_price);
            $simple->set_sku($variants_child_sku);
            $simple->set_category_ids($categories);
            $simple->set_manage_stock('yes');
            $simple->set_stock_quantity($stock);
            $simple->set_height($height);
            $simple->set_length($length);
            $simple->set_width($width);
            $simple->set_weight($weight);
            $simple->set_downloadable('no');
            //$variation->set_stock_quantity($stock);
            $simple->set_virtual('no');
            $simple->set_stock_status('instock');
            $simple->set_attributes(array('variants' => $variants_child_sku . " ". ltrim($variation_attributes,'') ));
            $simple->save();

        }
        else if($product_type == 'variable'){
            $variation = new WC_Product_Variation();
            $variation->set_name($name);
            $variation->set_parent_id($variation_parents_id);

            $thumb_id = $prd->dsi_set_thumbnail($images[0]);
            $variation->set_image_id($thumb_id);
            $variation->set_regular_price($regular_price);
            $variation->set_sale_price($sale_price);
            $variation->set_sku($variants_child_sku);
            $variation->set_manage_stock('yes');
            $variation->set_stock_quantity($stock);
            $variation->set_height($height);
            $variation->set_length($length);
            $variation->set_width($width);
            $variation->set_weight($weight);
            $variation->set_downloadable('no');
            //$variation->set_stock_quantity($stock);
            $variation->set_virtual('no');
            $variation->set_stock_status('instock');
            $variation->set_attributes(array('variants' => $variants_child_sku . " ". ltrim($variation_attributes,'') ));
            $variation->save();

            // The variation data
            
            //dsi_create_product_variation($variation_parents_id,$variation_data);
        }
        
        

        $status_message = 'Created';
    }
    $vp_sku = rtrim($variation_parents_sku,'-MAIN');
    echo json_encode([
        'data'=>[
            'product_id'=> $pid,
            'sku'=> $sku,
            'name'=> $name,
            'price'=> $regular_price,
            'category'=>$_POST['lines'][$_POST['selected_category']],
            'length'=> $length,
            'width'=> $width.
            ''
        ],
        'action'=>$action,
        'status_message'=>$status_message . "-" . $_POST['line_counter_'], 
        'selected_cat' => $_POST['selected_category'],
        'mark_up_base' => $_POST['mark_up_base'],
        'mark_up_value' => $_POST['mark_up_value'],
        'mark_up_perc' => $percent,
        'price_up'=> $regular_price,
        'parent_attribute_id' => $parent_attribute_id,
        'media' => get_attached_media( '', $pid ),
        'variation_parents_sku'=>$variation_parents_sku,
        'variants_child_name'=> $name,
        'variation_parent_id' => $variation_parents_id,
        'variation_parent_title'=>$variation_parent_title,
        'product_type' => $product_type,
        'variants_child_sku' => $variants_child_sku,
        'variation_attributes'=> $variation_attributes,
        'attributes_id' => $parent_attribute_id,
        'variation_attributes_terms'=>$_POST['variation_attribute_terms_'][$vp_sku],
        'teststring'=>$teststring
    ]);
    //delete_transient('dsi_trans_idropship');
    exit();
}
?>