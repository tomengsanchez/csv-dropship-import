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

<hr>

<fieldset >
    <form method='post'action="" class="" enctype='multipart/form-data'>
        <table class="table">
            <tr>
                <td><label for="dropship_company">Select Dropship Supplier</label></td>
                <td>
                    <select name="dropship_company" id="dropship_company">
                        <option value='aw-dropship'>AW DROPSHIP</option>
                        <option selected value='idropship'>i Dropship</option>
                    </select>
                </td>
            
                <td>
                    <label for="csv_file">Select CSV File</label>
                </td>
                <td>
                    <input type='file' name='csv_file' id='csv_file'>
                </td>
                <td>
                
                <button id='delete_all_p' class='button'>Delete All Product (Dev Purposes only)</button>
                <button id='test12' onclick='jQuery("#csv_file").trigger("change")' class='button'>Test Ajax Script</button>
                <div id='testDiv'></div>
                    <div id='delete_ajxdiv'>
                        
                    </div>
            </td>
            </tr>
        </table>
</fieldset>

<hr>
    <?php   
    $parent_title = '';
    $names = array(
        'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover Single Size',
        'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover Double Size',
        'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover King Size',
        'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover King Single',
        'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover Queen Size'
    );
    $names = array(
        '3-5 kg Himalayan Salt Lamp Rock Crystal Natural Light Dimmer Switch Cord Globes',
        '5-7 kg Himalayan Salt Lamp Rock Crystal Natural Light Dimmer Switch Cord Globes'
    );

    $names = [
        '4x1M Inflatable Air Track Mat Tumbling Pump Floor Home Gymnastics Gym in Red',
        '5x1M Air Track Inflatable Mat Airtrack Tumbling Electric Air Pump Gymnastics',
        '5x1M Air Track Inflatable Mat Airtrack Tumbling Electric Air Pump Gymnastics',
        '6x1M Air Track Inflatable Mat Airtrack Tumbling Electric Air Pump Gymnastics',
        '6x1M Air Track Inflatable Mat Airtrack Tumbling Electric Air Pump Gymnastics'

    ];
    
    
    $skus = array(
        'EE1501',
        'EE1501-D',
        'EE1501-K',
        'EE1501-KS',
        'EE1501-Q'
    );
    
    $x = 'DreamZ Fitted Wa
    terproof Mattress Protector with Bamboo Fibre Cover Single Size';

    $titles = array();
    $collected_titles = array();
    foreach($names as $n){
        $namesexp = explode(' ',$n);
        array_push($titles,$namesexp);
        foreach($namesexp as $nexp){
            array_push($collected_titles,$nexp);
        }
    }
    
    $count_arry = array_count_values($collected_titles);
    
    ar_to_pre($count_arry);

    foreach($count_arry as $k =>  $ca){
        if($ca == count($names))
            $parent_title .= $k . " ";
    }

    array_intersect($collected_titles);
    echo $parent_title;
    ?>

<div class='dsi-row' >

    <input type='hidden' class='loop-counter' value='0'>
    <div class="dsi-col">
    <h2 style='padding-left:35px'>IMPORT PARAMETERS</h2>
        <div class='csv-import-table-div' id='csv_ajax_table'></div>
    </div>
    <div class="dsi-col" style='min-height:400px'>
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
            <table width='600'>
                <tr>
                    <td width='300'><h3 style='padding-left:20px'><span class='import_files'>0</span> of <span class='read_files'>0</span > are Processed</h3></td>
                    <td width='100'><h3><b>Progress : </b></h3></td>
                    <td ><div style='width:200px' id='progressbar'></div></td>
                </tr>
            </table>
            <table class='dsi-table'   id='dsi-summary-table' style='width:600px;background-color:white'>
                <thead class='dsi-thead'>
                    <tr class='first-tr '>
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

 <?php ?>