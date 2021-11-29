<?php 
/**
 * Custom Functions
 */

function ar_to_pre($arr=array()){
    echo "<pre>";
    print_r($arr);
    echo  "</pre>";
    
}
/**
 * Covert CSV HEADING TO META FIELD FORMAT
 * 
 * @param array $array_heading array of heading to be formatted
 * 
 */
function conv_csv_heading_to_meta($array_heading){
    $formatted_heading = array();

    for($i = 0; $i < count($array_heading); $i++){
        $format = conv_string_to_meta($array_heading[$i]);
        array_push($formatted_heading,$format);
    }
    ar_to_pre($formatted_heading);
}
/**
 * convert Regular String to Meta
 * 
 * @param string $string Strings to Convert
 */
function conv_string_to_meta($string){
    $format = str_replace(' ','_',$string);// " " to _
    $format = str_replace('(','',$format);// "(" to ""
    $format = str_replace(')','',$format);// ")" to ""
    $format = str_replace('/','_',$format);// ")" to ""
    $format = strtolower($format);
    return "_" . $format;
}


function json_message($m){
    echo json_encode(['message'=>$m]);
}


function dsi_create_product_variation( $product_id, $variation_data ){
    // Get the Variable product object (parent)
    $product = wc_get_product($product_id);

    $variation_post = array(
        'post_title'  => $product->get_name(),
        'post_name'   => 'product-'.$product_id.'-variation',
        'post_status' => 'publish',
        'post_parent' => $product_id,
        'post_type'   => 'product_variation',
        'guid'        => $product->get_permalink()
    );

    // Creating the product variation
    $variation_id = wp_insert_post( $variation_post );

    // Get an instance of the WC_Product_Variation object
    $variation = new WC_Product_Variation( $variation_id );

    // Iterating through the variations attributes
    foreach ($variation_data['attributes'] as $attribute => $term_name )
    {
        $taxonomy = 'pa_'.$attribute; // The attribute taxonomy

        // If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
        if( ! taxonomy_exists( $taxonomy ) ){
            register_taxonomy(
                $taxonomy,
            'product_variation',
                array(
                    'hierarchical' => false,
                    'label' => ucfirst( $attribute ),
                    'query_var' => true,
                    'rewrite' => array( 'slug' => sanitize_title($attribute) ), // The base slug
                ),
            );
        }

        // Check if the Term name exist and if not we create it.
        if( ! term_exists( $term_name, $taxonomy ) )
            wp_insert_term( $term_name, $taxonomy ); // Create the term

        $term_slug = get_term_by('name', $term_name, $taxonomy )->slug; // Get the term slug

        // Get the post Terms names from the parent variable product.
        $post_term_names =  wp_get_post_terms( $product_id, $taxonomy, array('fields' => 'names') );

        // Check if the post term exist and if not we set it in the parent variable product.
        if( ! in_array( $term_name, $post_term_names ) )
            wp_set_post_terms( $product_id, $term_name, $taxonomy, true );

        // Set/save the attribute data in the product variation
        update_post_meta( $variation_id, 'attribute_'.$taxonomy, $term_slug );
    }

    ## Set/save all other data

    // SKU
    if( ! empty( $variation_data['sku'] ) )
        $variation->set_sku( $variation_data['sku'] );

    // Prices
    if( empty( $variation_data['sale_price'] ) ){
        $variation->set_price( $variation_data['regular_price'] );
    } else {
        $variation->set_price( $variation_data['sale_price'] );
        $variation->set_sale_price( $variation_data['sale_price'] );
    }
    $variation->set_regular_price( $variation_data['regular_price'] );

    // Stock
    if( ! empty($variation_data['stock_qty']) ){
        $variation->set_stock_quantity( $variation_data['stock_qty'] );
        $variation->set_manage_stock(true);
        $variation->set_stock_status('');
    } else {
        $variation->set_manage_stock(false);
    }
    
    $variation->set_weight(''); // weight (reseting)

    $variation->save(); // Save the data
}
add_action( 'init', 'disable_image_regeneration_process_20200804', 5 );
function disable_image_regeneration_process_20200804() {
   add_filter( 'woocommerce_background_image_regeneration', '__return_false' );
}
?>