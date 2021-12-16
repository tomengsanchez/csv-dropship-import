<?php 

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
    //ar_to_pre(generate_simple_product());
    ds_inserts();
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
    ));
    $product->save();
    return $product;
}

function ds_inserts(){
    
    $post_id = wp_insert_post( array(
        'post_title' => 'Great new product1212',
        'post_content' => 'Here is content of the post, so this is our great new products description',
        'post_status' => 'publish',
        'post_type' => "product",
    ) );
    wp_set_object_terms( $post_id, 'simple', 'product_type' );
    
    update_post_meta( $post_id, '_visibility', 'visible' );
    update_post_meta( $post_id, '_stock_status', 'instock');
    update_post_meta( $post_id, 'total_sales', '0' );
    update_post_meta( $post_id, '_downloadable', 'no' );
    update_post_meta( $post_id, '_virtual', 'yes' );
    update_post_meta( $post_id, '_regular_price', '' );
    update_post_meta( $post_id, '_sale_price', '' );
    update_post_meta( $post_id, '_purchase_note', '' );
    update_post_meta( $post_id, '_featured', 'no' );
    update_post_meta( $post_id, '_weight', '' );
    update_post_meta( $post_id, '_length', '' );
    update_post_meta( $post_id, '_width', '' );
    update_post_meta( $post_id, '_height', '' );
    update_post_meta( $post_id, '_sku', '' );
    update_post_meta( $post_id, '_product_attributes', array() );
    update_post_meta( $post_id, '_sale_price_dates_from', '' );
    update_post_meta( $post_id, '_sale_price_dates_to', '' );
    update_post_meta( $post_id, '_price', '' );
    update_post_meta( $post_id, '_sold_individually', '' );
    update_post_meta( $post_id, '_manage_stock', 'no' );
    update_post_meta( $post_id, '_backorders', 'no' );
    update_post_meta( $post_id, '_stock', '' );
}

add_action('wp_ajax_delete_all_products',function(){
    //var_dump($_POST);
    
    //require dirname(__FILE__).'/wp-blog-header.php';
    global $wpdb;
    $wpdb->query("DELETE FROM wp_terms WHERE term_id IN (SELECT term_id FROM wp_term_taxonomy WHERE taxonomy LIKE 'pa_%')");
    $wpdb->query("DELETE FROM wp_term_taxonomy WHERE taxonomy LIKE 'pa_%'");
    $wpdb->query("DELETE FROM wp_term_relationships WHERE term_taxonomy_id not IN (SELECT term_taxonomy_id FROM wp_term_taxonomy)");
    $wpdb->query("DELETE FROM wp_term_relationships WHERE object_id IN (SELECT ID FROM wp_posts WHERE post_type IN ('product','product_variation'))");
    $wpdb->query("DELETE FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_type IN ('product','product_variation'))");
    $wpdb->query("DELETE FROM wp_posts WHERE post_type IN ('product','product_variation')");
    $wpdb->query("DELETE pm FROM wp_postmeta pm LEFT JOIN wp_posts wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL");

    

    echo "All Products are Deleted";
    exit();
});


?>