<?php 
/** 
 * Load View of Dropship providers
 */



 ?>
<div class='wrap'>
    <?php 
        //select * product
        
        $sql = "SELECT * FROM " . $GLOBALS['wpdb']->prefix . "posts WHERE post_type = 'product'";
        $products = $GLOBALS['wpdb']->get_results($sql);
        
        //ar_to_pre($products);
        foreach($products as $prod){
            $p = new WC_Product_Variable($prod);
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
        //loop
            // delete all
    ?>
   
</div>

