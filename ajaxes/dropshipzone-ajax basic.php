<?php 
function get_csv_and_send_dropshipzone($csv_file,$wc_fields,$sample_data){
    header("Content-Type:application/json");
    delete_transient('updated_parent');
    $starttime = microtime(true);
    $prd = new DSI_Products();
    $csv = $_FILES['csv_file']['tmp_name'];// File NAme
    $fo = fopen($csv,'r');// Open the File
    $head = fgetcsv($fo,10000,','); // Read The Heading
    $prd->set_meta_to_import();

    $upload_mapping = array();  
    
    $prd->read_csv_lines($fo);
    $lns = $prd->data_per_lines;
    $variation_parents = array();
    $data_lines = array();
    $descriptions = array();
    
    for($l = 1; $l < count($lns) ; $l++){
        $title_ = trim($lns[$l-1][8]);
        $title_ = str_replace('-','wci_hypen',$title_);
        foreach($lns[$l-1] as $key =>$val){
            if($key == 14){
                // $desc = $lns[$l-1][8]; 
                $desc = $lns[$l-1][14];
                //$desc = str_replace(' ','',$desc);
                //$lns[$l-1][14] = json_encode($desc);

            }
        }
        array_push($data_lines,$lns[$l-1]);
        array_push($variation_parents, $title_ ."wci_split".strtoupper($lns[$l-1][0]));
    }
    
    //print_r($descriptions);
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
        $title = $title_exp[0];
        $variation_parents_processed_with_final_parent_name[$vpp] = $title;
    }
    
    $variation_attributes_terms = array();
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
                
                $attr_string .= $vexpst  . ":" . trim($sku_trim) ."|";
            }
        }
        
        $attr_string = rtrim($attr_string,'|');
        $attr_string = ltrim($attr_string,'|');
        $variation_attributes_terms[$key] = $attr_string;
    }
    
    //make variation parents unique
    
    $upload_mapping = array();  


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
    for($c = 1; $c < count($prd->data_per_lines); $c++){
        array_push($cats,$prd->data_per_lines[$c-1][3]);
    }
    $cats = array_unique($cats);
    // if(!in_array($name_heading, $prd->valid_name_heading)){
    //     $data_lines = array();
    // }

    //print_r($data_lines);
    echo json_encode([
        'row'=>$upload_mapping,
        'script'=> $script,
        'data_per_lines'=> $data_lines,
        'categories'=>$cats,
        'valid' => in_array($head[13],$prd->valid_headings['dropshipzone']),
        'variations'=>$variations,
        'variation_parents' => str_replace('wci_hypen','-',$variation_parents_processed),
        'variation_parents_title'=>str_replace('wci_hypen','-',$variation_parents_title),
        'variation_parent_with_title'=>str_replace('wci_hypen','-',$variation_parents_processed_with_final_parent_name),
        'variation_attribute_terms'=>str_replace('wci_hypen','-',$variation_attributes_terms)
        ]
    );
    //set_transient('dropshipzone_description',json_encode($descriptions));

}

add_action('wp_ajax_get_field_then_import_dropshipzone','get_field_then_import_dropshipzone');

function get_field_then_import_dropshipzone(){
    header('Content-Type:application/json');
    $starttime = microtime(true);
    // $desciption_trans = get_transient('dropshipzone_description');
    // $desciption_trans = json_decode($desciption_trans);
    // $_POST['lines'][8] = $desciption_trans[$_POST['line_counter_']];
    // $_POST['transient_count'] = count($desciption_trans)-1;

    $name = $_POST['lines'][13];
    $name = trim($name);
    $type = $_POST['lines'][1];
    
    $sku = $_POST['lines'][0];
    $sku = strtoupper($sku);
    $description = $_POST['lines'][14];
    $description = str_replace('\\','',$description);
    $description = str_replace('"','',$description);
    //$description = 'to be Followed' . $sku;
    
    $status = $_POST['lines'][4];
    $regular_price = $_POST['lines'][1];
    $regular_price = number_format($regular_price,2);
    $sale_price = $_POST['lines'][2];
    $sale_price = number_format($sale_price,2);
    $brand = $_POST['lines'][34];
    $weight = $_POST['lines'][9];
    $length = $_POST['lines'][10];
    $width = $_POST['lines'][11];
    $height = $_POST['lines'][12];
    $image = $_POST['lines'][15];
    $stock = $_POST['lines'][6];
    $category = $_POST['lines'][16];
    $sale_date = $_POST['lines'][3];
    $sale_date_end = $_POST['lines'][4];
    $sale_date_end = explode(' ',$sale_date_end)[0];
    //$low_stock = $_POST['lines'][15];
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

    $percent = $_POST['mark_up_value']/100;
    if($_POST['mark_up_base'] == 'None'){
        $regular_price = $regular_price;
        $sale_price = $sale_price;
    }
    else if($_POST['mark_up_base'] == 'Price $'){
        $regular_price = $regular_price + $_POST['mark_up_value'];
        $sale_price = $sale_price + $_POST['mark_up_value'];
    }
    else if($_POST['mark_up_base'] == 'Percentage %'){
        $regular_price = $regular_price + ($regular_price * $percent);
        $sale_price = $sale_price + ($sale_price * $percent);
    }
    
    $regular_price = number_format($regular_price,2);
    $sale_price = number_format($sale_price,2);

    $prd = new DSI_Products();
    $update_id = '';
    if(!empty($_POST['lines'][50])){
        $category .= "," . $_POST['lines'][50];
    }
    
    $categories = $prd->category_manipulation_nested($category);


    
    $q_get_existing = 'SELECT * FROM ' . $GLOBALS['wpdb']->prefix . 'postmeta WHERE meta_value="' . $sku . '" AND meta_key ="_sku"';
    $product_existing = $GLOBALS['wpdb']->get_results($q_get_existing);
    $existing = 0;
    if(count($product_existing) == 1){
        $existing = 1;
        $update_id = $product_existing[0]->post_id;
    }

    
    
    // // work with categories

    // //$images = array();
    

    // $xc = 0;
    
    if($existing == 1){ // IF SKU IS 
        $action = 'update';

        //exit();
        if($_POST['skip_existing_sku_yes']=='false'){
            
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
            //global $wpdb;

            //$table = $wpdb->prefix. "posts";
            if($_POST['upload_images_yes'] == 'true'){
            
                $p = new WC_Product_Simple($update_id);
                //print_r($p);
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
                    $gallery_image_ids[$i];
                    $attachment_path = get_attached_file( $gallery_image_ids[$i]); 
                    //Delete attachment from database only, not file
                    $delete_attachment = wp_delete_attachment($gallery_image_ids[$i], true);
                    //Delete attachment file from disk
                    $delete_file = unlink($attachment_path);
                }
            }
            


            $simple = new WC_Product_Simple($update_id);
            $simple->set_name($name);
            //$variation->set_parent_id($variation_parents_id);
            
            $thumb_id = $prd->dsi_set_thumbnail($images[0],$sku);
            $simple->set_image_id($thumb_id);
            $imgs_id = $prd->dsi_set_image_gallery($images,$sku);
            $imgs_id_imp = implode(',', $imgs_id);
            $simple->set_gallery_image_ids($imgs_id_imp);
            $simple->set_regular_price($regular_price);
            $simple->set_sale_price($sale_price);
            $simple->set_sku($sku);
            $simple->set_category_ids($categories);
            $simple->set_description($description);
            $simple->set_manage_stock('yes');
            $simple->set_date_on_sale_from($sale_date);
            $simple->set_date_on_sale_to($sale_date_end);
            $simple->set_low_stock_amount($low_stock);
            $simple->set_stock_quantity($stock);
            $simple->set_height($height);
            $simple->set_length($length);
            $simple->set_width($width);
            $simple->set_weight($weight);
            $simple->set_downloadable('no');
            //$variation->set_stock_quantity($stock);
            $simple->set_virtual('no');
            $simple->save();
            

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
        
        if($_POST['upload_images_yes'] == 'true'){
            
            $p = new WC_Product_Simple($update_id);
            //print_r($p);
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
                $gallery_image_ids[$i];
                $attachment_path = get_attached_file( $gallery_image_ids[$i]); 
                //Delete attachment from database only, not file
                $delete_attachment = wp_delete_attachment($gallery_image_ids[$i], true);
                //Delete attachment file from disk
                $delete_file = unlink($attachment_path);
            }
        }
            
        $simple = new WC_Product_Simple();
        
        $simple->set_name($name);
        //$variation->set_parent_id($variation_parents_id);

        $thumb_id = $prd->dsi_set_thumbnail($images[0],$sku);
        $simple->set_image_id($thumb_id);
        $imgs_id = $prd->dsi_set_image_gallery($images,$sku);
        $imgs_id_imp = implode(',', $imgs_id);
        $simple->set_gallery_image_ids($imgs_id_imp);
        $simple->set_regular_price($regular_price);
        $simple->set_sale_price($sale_price);
        $simple->set_sku($sku);
        $simple->set_description($description);
        $simple->set_category_ids($categories);
        $simple->set_manage_stock('yes');
        $simple->set_stock_quantity($stock);
        $simple->set_date_on_sale_from($sale_date);
        $simple->set_date_on_sale_to($sale_date_end);
        $simple->set_low_stock_amount($low_stock);
        $simple->set_height($height);
        $simple->set_length($length);
        $simple->set_width($width);
        $simple->set_weight($weight);
        $simple->set_downloadable('no');
        
        $simple->set_virtual('no');
        $simple->set_stock_status('instock');
        $simple->set_attributes(array('variants' => $name . ":" .  $sku) );
        $simple->save();
        // The variation data
        
        //dsi_create_product_variation($variation_parents_id,$variation_data);
    $status_message = 'Created';
    }
    
    $endtime = microtime(true);

    echo json_encode([
        'data'=>[
            'product_id'=> $update_id,
            'sku'=> $sku,
            'name'=> $name,
            'price'=> $regular_price,
            'category'=>$_POST['lines'][$_POST['selected_category']],
            'length'=> $length,
            'width'=> $width,
            'begin_sale' =>$sale_date,
            'end_sale' =>$sale_date_end
        ],
        'action'=>$action,
        'status_message'=>$status_message, 
        'selected_cat' => $_POST['selected_category'],
        'mark_up_base' => $_POST['mark_up_base'],
        'mark_up_value' => $_POST['mark_up_value'],
        'mark_up_perc' => $percent,
        'price_up'=> $regular_price,
        
        'loading_time'=> number_format($endtime - $starttime,2)
    ]);

    if($_POST['line_counter_'] == $_POST['transient_count']){
        delete_transient('dropshipzone_description');
        //echo "Deleted Trans";
    }
    exit();
}
?>