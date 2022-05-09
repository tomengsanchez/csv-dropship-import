<?php 
/** main Page - /admin.php?page=dropship-import-page */

use Automattic\WooCommerce\Admin\API\Products;

?>
<?php 
// $id = '34077';
// $p = new WC_Product($id);


// ar_to_pre($p->get_gallery_image_ids());

// $gallery_image_ids= $p->get_gallery_image_ids();

// for($i = 0; $i < count($gallery_image_ids) ; $i++){
    
//     $attachment_path = get_attached_file( $gallery_image_ids[$i]); 
//     //Delete attachment from database only, not file
//     $delete_attachment = wp_delete_attachment($gallery_image_ids[$i], true);
//     //Delete attachment file from disk
//     $delete_file = unlink($attachment_path);
// }

?>
<h1 class="h1">Dropship CSV File Import</h1>
<i>Lets you create or update your WooCommerce Products using a CSV file provided by the Dropship supplier.</i>

<?php 
    
    echo $p = get_post(89642)->post_type;
    

    // print_r($p);
    // $attachmentID= $p->get_image_id();
    // //wp_delete_attachment('34041', true);
    // $attachment_path = get_attached_file( $attachmentID); 
    // //Delete attachment from database only, not file
    // $delete_attachment = wp_delete_attachment($attachmentID, true);
    // //Delete attachment file from disk
    // $delete_file = unlink($attachment_path);
    // //delete all gallery images
    // $gallery_image_ids= $p->get_gallery_image_ids();
    // for($i = 0; $i <= count($gallery_image_ids) ; $i++){
    //     $gallery_image_ids[$i];
    //     $attachment_path = get_attached_file( $gallery_image_ids[$i]); 
    //     //Delete attachment from database only, not file
    //     $delete_attachment = wp_delete_attachment($gallery_image_ids[$i], true);
    //     //Delete attachment file from disk
    //     $delete_file = unlink($attachment_path);
    // }
    global $wpdb;
    

    // $dim_default = "D:6.7(cm), 0.221L, 0.8Kg/L";

    // $dim = explode(',',$dim_default)[0];
    // $dim = trim($dim,'(mm)');
    // $dim = trim($dim,'(cm)');
    // $dim = explode('x',$dim);
    // $lnt = $dim[0];
    // $wdt = $dim[1];
    // $ht  = $dim[2];
    // if(strpos($dim_default,'(mm)')){
    //     if(count(explode('D:',$dim_default))>1){
    //         $lnt = preg_replace("/[^0-9\.]/", '', $dim[0])/1000 * 1;
    //         $wdt = preg_replace("/[^0-9\.]/", '', $dim[0])/1000 * 1;
    //         $ht  = preg_replace("/[^0-9\.]/", '', $dim[0])/1000 * 1;
    //     }
    //     else{
    //         $lnt = $dim[0]/1000;
    //         $wdt = $dim[1]/1000;
    //         $ht  = $dim[2]/1000;
    //     }
        
    // }
    // else if (strpos($dim_default,'(cm)')){
    //     if(count(explode('D:',$dim_default))>1){
    //         $lnt = preg_replace("/[^0-9\.]/", '', $dim[0]);
    //         $wdt = preg_replace("/[^0-9\.]/", '', $dim[0]);
    //         $ht  = preg_replace("/[^0-9\.]/", '', $dim[0]);
    //     }
    //     else{

    //         $lnt = $dim[0];
    //         $wdt = $dim[1];
    //         $ht  = $dim[2];
    //     }
        
        
    // }
    // else{
        
    //     $diameter = explode(',',$dim_default)[0];
        
    //     $string = $diameter;
    //     $diam =  preg_replace("/[^0-9\.]/", '', $string);

    //     $lnt = $diam;
    //     $wdt = $diam;
    //     $ht  = $diam;

    //     if(strpos($diameter,'mm')){
    //         $lnt = $lnt / 1000;
    //         $wdt = $wdt / 1000;
    //         $ht = $ht /1000;
    //     }
        

    // }
    // echo "<br>" . $lnt . "x" . $wdt . "x" . $ht;
?>
<hr>

<fieldset >
    <form method='post'action="" class="" enctype='multipart/form-data'>
        <table class="table">
            <tr>
                <td><label for="dropship_company">Select Dropship Supplier</label></td>
                <td>
                    <select name="dropship_company" id="dropship_company">
                        <option selected value=''>Please Select</option>
                        <option value='aw-dropship'>AW DROPSHIP</option>
                        <option selected value='idropship'>i Dropship</option>
                        <option  value='dropshipzone'>Dropship Zone</option>
                    </select>
                </td>

                <td>
                    <label for="csv_file">Select CSV File</label>
                </td>
                <td>
                    <input type='file' name='csv_file' id='csv_file'>
                </td>
                <td>
                    <button disabled id='delete_all_p' class='button'>Delete All Product (Dev Purposes only)</button>
                    <button disabled id='test12' onclick='jQuery("#csv_file").trigger("change")' class='button'>Test Ajax Script</button>
                    <div id='testDiv'></div>
                    <div id='delete_ajxdiv'>
                        
                    </div>
                </td>
            </tr>
        </table>
</fieldset>

<hr>
    <?php  
        // $variable = new WC_Product_Variable(88647);

        // ar_to_pre($variable->attributes['variants']['options']);
        

    ?>
<!-- <div class="container"> -->
    <div class='dsi-row row' >
        <input type='hidden' class='loop-counter' value='0'>
        <div class="dsi-col col-sm-6">
        <h2 style='padding-left:35px'>IMPORT PARAMETERS</h2>
            <div class="container">
                <div class='csv-import-table-div row' id='csv_ajax_table'></div >
            </div>
            
        </div>
        <div class="dsi-col col-sm-6" style='min-height:400px'>
        <h2 style='padding-left:20px'>IMPORT STATUS</h2>
            <?php 
            
            ?>
            <script >
                jQuery(document).ready(function(){
                    jQuery( "#progressbar" ).progressbar();
                }); 
                jQuery('.progress').change(function(){
                    alert(1);
                });
            </script>
            <div class="import-result" style='margin-top:118px'>
                <input type='hidden' id='row_holder_finish'>
                <input type='hidden' id='row_holder_start' value='0'>
                <table width='600' class='table'>
                    <tr>
                        <td width='300'><h3 style='padding-left:20px'><span class='import_files'>0</span> of <span class='read_files'>0</span > are Processed</h3></td>
                        <td width='100'><h3><b>Progress : </b></h3></td>
                        <td ><div style='width:200px' id='progressbar'></div></td>
                    </tr>
                </table>
                <table class='dsi-table table'   id='dsi-summary-table' style='width:600px;background-color:white'>
                    <thead class='dsi-thead'>
                        <tr class='first-tr'>
                            <th>Sku</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                        <tbody>
                    </thead>
                </table>      
            </div>
        </div>
    </div>
<!-- </div> -->

 <?php 


//delete_transient('dsi_trans_idropship');
//ar_to_pre(get_transient('dsi_trans_idropship'));

 ?>