<?php 
function get_csv_and_send_idropship($csv_file,$wc_fields,$sample_data){
    header("Content-Type:application/json");
    delete_transient('updated_parent');
    $prd = new DSI_Products();
    $csv = $_FILES['csv_file']['tmp_name'];// File NAme
    $fo = fopen($csv,'r');// Open the File
    $head = fgetcsv($fo,10000,','); // Read The Heading
    $prd->set_meta_to_import();
    
    // Variables
    
    $cats= array();
    
    $prd->read_csv_lines($fo);
    $lns = $prd->data_per_lines;
    
    $variation_parents = array();
    $data_lines = array();
    //print_r($lns);
    
    for($l = 1; $l < count($lns) ; $l++){
        array_push($data_lines,$lns[$l-1]);
        array_push($variation_parents,$lns[$l-1][3] ."wci_split".$lns[$l-1][2]);
        
    }
    
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
    //print_r($variation_parents_processed);
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
                
                $attr_string .= $vexpst  . ":" . $sku_trim ."|";
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

    //REsults
    for($c = 1; $c < count($prd->data_per_lines); $c++){
        array_push($cats,$prd->data_per_lines[$c-1][3]);
    }
    $cats = array_unique($cats);
    
    if(!in_array($name_heading, $prd->valid_name_heading)){
        $data_lines = array();
    }
    //$script = '';
    echo json_encode([
        'row'=>$upload_mapping,
        'script'=> $script,
        'data_per_lines'=> $data_lines,
        'categories'=>$cats,
        'valid' => in_array($head[39],$prd->valid_headings['idropship']),
        'variations'=>$variations,
        'variation_parents' => $variation_parents_processed,
        'variation_parents_title'=>$variation_parents_title,
        'variation_parent_with_title'=>$variation_parents_processed_with_final_parent_name,
        'variation_attribute_terms'=>$variation_attributes_terms
        ]
    );
    exit();
}

add_action('wp_ajax_get_field_then_import_idropship','get_field_then_import_idropship');

function get_field_then_import_idropship(){
    
    $starttime = microtime(true);
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
    $regular_price = number_format($regular_price,2);
    $sale_price = $_POST['lines'][24];
    $sale_price = number_format($sale_price,2);
    $brand = $_POST['lines'][34];
    $weight = $_POST['lines'][18];
    $length = $_POST['lines'][19];
    $width = $_POST['lines'][20];
    $height = $_POST['lines'][21];
    $image = $_POST['lines'][29];
    $stock = $_POST['lines'][14];
    $category = $_POST['lines'][26];
    $sale_date = $_POST['lines'][9];
    $low_stock = $_POST['lines'][15];
    $thumbnail1 = '';
    $action = '';
    $product_type = 'simple';
    $images = array();
    $image_array = explode(',',$image);
    $variable_sku = $_POST['lines'][57];
    $variable_title = $_POST['lines'][58];

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
    }
    else if($_POST['mark_up_base'] == 'Price $'){
        $regular_price = $regular_price + $_POST['mark_up_value'];
    }
    else if($_POST['mark_up_base'] == 'Percentage %'){
        $regular_price = $regular_price + ($regular_price * $percent);
    }
    $regular_price = number_format($regular_price,2);

    $prd = new DSI_Products();
    $update_id = '';
    //array_push($category,$_POST['lines'][56]);
    $category .= "," . $_POST['lines'][56];
    $categories = $prd->category_manipulation_nested($category);
    
    //product type
    $sku_0 = explode('-',$sku)[0];
    $variation_parent = '';
    $variation_attributes = '';
    
    
    
    $q_get_existing = 'SELECT * FROM ' . $GLOBALS['wpdb']->prefix . 'postmeta WHERE meta_value="' . $sku . '" AND meta_key ="_sku"';
    $product_existing = $GLOBALS['wpdb']->get_results($q_get_existing);
    $existing = 0;
    if(count($product_existing) == 1){
        $existing = 1;
        $update_id = $product_existing[0]->post_id;
    }

    if($variable_sku == ''){// IF has no parent skue\
        //insert/update as simple

        $action = 'Create Simple Product';
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

    }
    else{//else
        $action = 'Check Variable';
        $variable_id = wc_get_product_id_by_sku($variable_sku);
                
        
            $action = 'added variable';
            $product = new WC_Product_Variable($variable_id);
            $product->set_name($variable_title);
            $product->set_sku($variable_sku);
            $product->set_short_description($description);
            $product->set_description($description);
            
            $thumb_id = $prd->dsi_set_thumbnail($images[0],$variable_sku);
            $product->set_image_id($thumb_id);

            $imgs_id = $prd->dsi_set_image_gallery($images,$variable_sku);
            $imgs_id_imp = implode(',', $imgs_id);
            $product->set_gallery_image_ids($imgs_id_imp);
            $attribute = new WC_Product_Attribute();
            $attribute->set_id(0);
            $attribute->set_name('variants');

            $var_options = $product->attributes['variants']['options'];
            array_push($var_options,$name);
            $attribute->set_options($var_options);
            $attribute->set_visible(false);
            $attribute->set_variation(true);
            
            $product->set_attributes(array($attribute));
            $variable_id = $product->save();        
            
            
        
        
        
        if($existing == 1){
            
            $action = 'edit variation';
            $variation = new WC_Product_Variation($update_id);
            $variation->set_name($name);
            $variation->set_parent_id($variable_id);
            
            $prd_update = new DSI_Products();
            $thumb_id = $prd_update->dsi_set_thumbnail($images[0],$sku);
            // $variation->set_image_id($thumb_id);
            update_post_meta($update_id,'_thumbnail_id',$thumb_id);
            
            //$variation->set_image_id($thumb_id);
            // $imgs_id = $prd->dsi_set_image_gallery($images);
            // $imgs_id_imp = implode(',', $imgs_id);
            // $variation->set_gallery_image_ids($imgs_id_imp);
            $variation->set_regular_price($regular_price);
            $variation->set_sale_price($sale_price);
            $variation->set_sku($sku);
            $variation->set_category_ids($categories);
            $variation->set_description($description);
            $variation->set_manage_stock('yes');
            $variation->set_date_on_sale_from($sale_date);
            $variation->set_date_on_sale_to($sale_date_end);
            $variation->set_low_stock_amount($low_stock);
            $variation->set_stock_quantity($stock);
            $variation->set_height($height);
            $variation->set_length($length);
            $variation->set_width($width);
            $variation->set_weight($weight);
            $variation->set_downloadable('no');
            //$variation->set_stock_quantity($stock);
            $variation->set_virtual('no');
            $variation->set_stock_status('instock');
            $variation->set_attributes(array('variants' => $name));
            $variation->save();
        }
        else{
            

            $action = 'insert variation';
            $variation = new WC_Product_Variation($update_id);
            $variation->set_name($name);
            $variation->set_parent_id($variable_id);
            $prd_insert = new DSI_Products();
            $thumb_id = $prd_insert->dsi_set_thumbnail($images[0],$sku);
            // $variation->set_image_id($thumb_id);
            $variation->set_image_id($thumb_id);
            // $imgs_id = $prd->dsi_set_image_gallery($images);
            // $imgs_id_imp = implode(',', $imgs_id);
            // $variation->set_gallery_image_ids($imgs_id_imp);
            $variation->set_regular_price($regular_price);
            $variation->set_sale_price($sale_price);
            $variation->set_sku($sku);
            $variation->set_category_ids($categories);
            $variation->set_description($description);
            $variation->set_manage_stock('yes');
            $variation->set_date_on_sale_from($sale_date);
            $variation->set_date_on_sale_from($sale_date_end);
            $variation->set_low_stock_amount($low_stock);
            $variation->set_stock_quantity($stock);
            $variation->set_height($height);
            $variation->set_length($length);
            $variation->set_width($width);
            $variation->set_weight($weight);
            $variation->set_downloadable('no');
            //$variation->set_stock_quantity($stock);
            $variation->set_virtual('no');
            $variation->set_stock_status('instock');
            $variation->set_attributes(array('variants' => $name));
            $variation->save();
        }

        $var_att_update = new WC_Product_Variable($variable_id);
        

        $attribute = new WC_Product_Attribute();
        $attribute->set_id(0);
        $attribute->set_name('variants');

        $var_options = $var_att_update->attributes['variants']['options'];
        array_push($var_options,$name);
        $attribute->set_options($var_options);
        $attribute->set_visible(false);
        $attribute->set_variation(true);
        
        $var_att_update->set_attributes(array($attribute));
        $variable_id = $var_att_update->save();        
        
        

    }   



    

    
    // // work with categories

    // //$images = array();
    
    $endtime = microtime(true);
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
        'status_message'=>$status_message, 
        'selected_cat' => $_POST['selected_category'],
        'mark_up_base' => $_POST['mark_up_base'],
        'mark_up_value' => $_POST['mark_up_value'],
        'mark_up_perc' => $percent,
        'price_up'=> $regular_price,
        'media' => get_attached_media( '', $pid ),
        'teststring'=>$teststring,
        'loading_time'=> number_format($endtime - $starttime,2)
    ]);
    //delete_transient('dsi_trans_idropship');
    exit();
}

?>