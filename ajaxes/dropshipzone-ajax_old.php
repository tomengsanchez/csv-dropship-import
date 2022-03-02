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
    
    $q_get_existing = 'SELECT * FROM ' . $GLOBALS['wpdb']->prefix . 'postmeta WHERE meta_value="' . $sku . '" AND meta_key ="_sku"';
    $product_existing = $GLOBALS['wpdb']->get_results($q_get_existing);
    $existing = 0;
    if(count($product_existing) == 1){
        $existing = 1;
        $update_id = $product_existing[0]->post_id;
    }

    $variation_parents_sku = $prd->process_variation_parent($sku,$_POST['variation_parents_'],$_POST['lines']);
    $variation_parents_loop = wc_get_products([
        'sku'=>$variation_parents_sku
    ]);

    
    if($product_type == 'variable' ){
        
        //echo $variation_parents_id;
        if($variation_parents_loop == null){
            //VARIABLE
            if($existing == 1){
                //echo "Update Variable: " . $variation_parents_sku;
                if($_POST['skip_existing_sku_yes']=='false'){
                    echo "Update Variable";
                    $q = 'SELECT * FROM ' . $GLOBALS['wpdb']->prefix . 'postmeta WHERE meta_value="'  . $variation_parents_sku  . '"';
                    $d = $GLOBALS['wpdb']->get_results($q);
                    $variation_parents_id = $d[0]->post_id;
                    echo "Update Variation : " . $variation_parents_sku . "-" . $variation_parents_id;
                    if($_POST['upload_images_yes'] == 'true'){
                        //delete_ main image
                        // $p = new WC_Product($variation_parents_id);
                        // $attachmentID= $p->get_image_id();
                        // //wp_delete_attachment('34041', true);
                        // $attachment_path = get_attached_file( $attachmentID); 
                        // //Delete attachment from database only, not file
                        // $delete_attachment = wp_delete_attachment($attachmentID, true);
                        // //Delete attachment file from disk
                        // $delete_file = unlink($attachment_path);
                        // //delete all gallery images
                        // $gallery_image_ids= $p->get_gallery_image_ids();
        
                    }
                    //echo "Update Variable sa TAAS";
                    
                    $product = new WC_Product_Variable($variation_parents_id);
                    $product->set_name($variation_parent_title);
                    $product->set_sku($variation_parents_sku);
                    $product->set_short_description($description);
                    $product->set_description($description);
                    $thumb_id = $prd->dsi_set_thumbnail($images[0]);
                    
                    $product->set_image_id($thumb_id);
                    // $imgs_id = $prd->dsi_set_image_gallery($images);
                    // $imgs_id_imp = implode(',', $imgs_id);
                    // $product->set_gallery_image_ids($imgs_id_imp);
                    $attribute = new WC_Product_Attribute();
                    $attribute->set_id(0);
                    $attribute->set_name('variants');
                    $vp_sku = rtrim($variation_parents_sku,'-MAIN');
                    echo "vp_sku : " . $vp_sku;
                    echo $_POST['variation_attribute_terms_'][$vp_sku];
                    $attribute->set_options(explode(WC_DELIMITER, $_POST['variation_attribute_terms_'][$vp_sku]));
                    
                    $product->set_category_ids($categories);
                    $attribute->set_visible(true);
                    $attribute->set_variation(true);
                    
                    $product->set_attributes(array($attribute));
                    
                    $product->save();
                    
                    $updated_parents = get_transient('updated_parent');
                    $updated_parents .= $variation_parents_id. ",";
                    set_transient('updated_parent',$updated_parents);
                }
            }
            else{
                /** INSERT SCRIPT */
                $parent_args = [
                    'name'=>$variation_parent_title,
                    'sku'=>$variation_parents_sku,
                    'category'=>$categories
                ];
                $variation_parents_id = $prd->insert_new_variation_parent($parent_args);

                $product = new WC_Product_Variable($variation_parents_id);
                $thumb_id = $prd->dsi_set_thumbnail($images[0],$sku);
                $product->set_image_id($thumb_id);
                $imgs_id = $prd->dsi_set_image_gallery($images,$sku);
                $imgs_id_imp = implode(',', $imgs_id);
                $product->set_gallery_image_ids($imgs_id_imp);
                $product->set_description($description);
                $attribute = new WC_Product_Attribute();
                $attribute->set_id(0);
                $attribute->set_name('variants');
                
                $vp_sku = str_replace('-MAIN','',$variation_parents_sku);

                

                $attribute->set_options(explode(WC_DELIMITER, $_POST['variation_attribute_terms_'][$vp_sku]));
                
                $product->set_category_ids($categories);
                $attribute->set_visible(true);
                $attribute->set_variation(true);
                
                $product->set_attributes(array($attribute));
                
                $variation_parents_id=$product->save();

                
                $updated_parents = get_transient('updated_parent');
                $updated_parents .= $variation_parents_id. ",";
                set_transient('updated_parent',$updated_parents);
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

            $updated= get_transient('updated_parent');
            $updated = rtrim($updated,',');
            $exp_updated = explode(',',$updated);
            if(!in_array($variation_parents_id,$exp_updated)){
                $q = 'SELECT * FROM ' . $GLOBALS['wpdb']->prefix . 'postmeta WHERE meta_value="'  . $variation_parents_sku  . '"';
                $d = $GLOBALS['wpdb']->get_results($q);
                $variation_parents_id = $d[0]->post_id;
                if($_POST['upload_images_yes'] == 'true'){
                    //delete_ main image
                    $p = new WC_Product_Variable($variation_parents_id);
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
                //echo "Update Variable sa BABA : ". $variation_parents_id .":"; 
                
                $product = new WC_Product_Variable($variation_parents_id);
                $product->set_name($variation_parent_title);
                $product->set_sku($variation_parents_sku);
                $product->set_short_description($description);
                $product->set_description($description);
                
                $thumb_id = $prd->dsi_set_thumbnail($images[0],$sku);
                $product->set_image_id($thumb_id);

                $imgs_id = $prd->dsi_set_image_gallery($images,$sku);
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
                
                $product->save();
                
                $updated_parents = get_transient('updated_parent');
                $updated_parents .= $variation_parents_id. ",";
                set_transient('updated_parent',$updated_parents);
            }
            else{
                
            }

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
            if($product_type=='simple'){
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
                $simple->set_sku($variants_child_sku);
                $simple->set_category_ids($categories);
                $simple->set_description($description);
                $simple->set_manage_stock('yes');
                $simple->set_date_on_sale_from($sale_date);
                $simple->set_low_stock_amount($low_stock);
                $simple->set_stock_quantity($stock);
                $simple->set_height($height);
                $simple->set_length($length);
                $simple->set_width($width);
                $simple->set_weight($weight);
                $simple->set_downloadable('no');
                //$variation->set_stock_quantity($stock);
                $simple->set_virtual('no');
                $simple->set_stock_status('instock');
                $simple->set_attributes(array('variants' => $name . ":" .  $sku) );
                $simple->save();
            }
            else{
                // echo "Update Variation : " . $sku . "-" . $update_id;
                //$prd->update_product_raw_sql($table,$args);
                $variation = new WC_Product_Variation($update_id);
                $variation->set_name($name);
                $variation->set_parent_id($variation_parents_id);

                $thumb_id = $prd->dsi_set_thumbnail($images[0],$sku);
                // $variation->set_image_id($thumb_id);
                update_post_meta($update_id,'_thumbnail_id',$thumb_id);
                // $imgs_id = $prd->dsi_set_image_gallery($images);
                // $imgs_id_imp = implode(',', $imgs_id);
                // $variation->set_gallery_image_ids($imgs_id_imp);
                $variation->set_regular_price($regular_price);
                $variation->set_sale_price($sale_price);
                $variation->set_sku($variants_child_sku);
                $variation->set_category_ids($categories);
                $variation->set_description($description);
                $variation->set_manage_stock('yes');
                $variation->set_date_on_sale_from($sale_date);
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
                $variation->set_attributes(array('variants' => $name . ":" .  $sku) );
                $variation->save();
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
        
        if($product_type == 'simple'){
            
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
            $simple->set_sku($variants_child_sku);
            $simple->set_description($description);
            $simple->set_category_ids($categories);
            $simple->set_manage_stock('yes');
            $simple->set_stock_quantity($stock);
            $simple->set_date_on_sale_from($sale_date);
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
        else if($product_type == 'variable'){
            $variation = new WC_Product_Variation();
            $variation->set_name($name);
            $variation->set_parent_id($variation_parents_id);

            $thumb_id = $prd->dsi_set_thumbnail($images[0],$sku);
            $variation->set_image_id($thumb_id);
            $variation->set_regular_price($regular_price);
            $variation->set_sale_price($sale_price);
            $variation->set_sku($variants_child_sku);
            $variation->set_manage_stock('yes');
            $variation->set_description($description);
            $variation->set_stock_quantity($stock);
            $variation->set_date_on_sale_from($sale_date);
            $variation->set_low_stock_amount($low_stock);
            $variation->set_height($height);
            $variation->set_length($length);
            $variation->set_width($width);
            $variation->set_weight($weight);
            $variation->set_downloadable('no');
            //$variation->set_stock_quantity($stock);
            $variation->set_virtual('no');
            $variation->set_stock_status('instock');
            $variation->set_attributes(array('variants' => $name . ":" .  $sku) );
            $variation->save();

            // The variation data
            
            //dsi_create_product_variation($variation_parents_id,$variation_data);
        }
        
        

        $status_message = 'Created';
    }
    $vp_sku = rtrim($variation_parents_sku,'-MAIN');
    $endtime = microtime(true);

    echo json_encode([
        'data'=>[
            'product_id'=> $update_id,
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
        'teststring'=>$teststring,
        'loading_time'=> number_format($endtime - $starttime,2)
    ]);

    if($_POST['line_counter_'] == $_POST['transient_count']){
        delete_transient('dropshipzone_description');
        //echo "Deleted Trans";
    }
    exit();
}
?>