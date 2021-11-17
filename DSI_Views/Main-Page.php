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

?>
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
     
    //$wch = new WC_Helper();
    
    // Objectives ...Remove the attribute from the each names to make the parent_title.
    // Use these 3 sample scenarios of names
    // Parent for 1. 'DreamZ Fitted Waterproof Mattres Protectr with Ramboo Filbre Cover'
    //$parent_title = '';

    $names = array(
        'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover Single Size',
        'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover Double Size',
        'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover King Size',
        'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover King Single',
        'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover Queen Size'
    );
    // Parent  for 2. 'Himalayan Salt Lamp Rock Crystal Natural Light Dimmer Cord Globes'
    $names = array(
        '3-5 kg Himalayan Salt Lamp Rock Crystal Natural Light Dimmer Switch Cord Globes',
        '5-7 kg Himalayan Salt Lamp Rock Crystal Natural Light Dimmer Switch Cord Globes'
    );
    // Parent  for 2. 'Air Track Inflatable Mat Airtrack Tumbling Electric Air Pump Gymnastics'
    $names = [  
        '4x1M Inflatable Air Track Mat Tumbling Pump Floor Home Gymnastics Gym in Red',
        '5x1M Air Track Inflatable Mat Airtrack Tumbling Electric Air Pump Gymnastics',
        '5x1M Air Track Inflatable Mat Airtrack Tumbling Electric Air Pump Gymnastics',
        '6x1M Air Track Inflatable Mat Airtrack Tumbling Electric Air Pump Gymnastics',
        '6x1M Air Track Inflatable Mat Airtrack Tumbling Electric Air Pump Gymnastics'
    ];
    $names = [
        "Levede Bed Frame Double King Fabric With Drawers Storage Wooden Mattress Grey",
        "Levede Bed Frame King Fabric With Drawers Storage Beige",
        "Levede Bed Frame Double King Fabric With Drawers Storage Wooden Mattress Grey",
        "Levede Bed Frame  Queen Fabric With Drawers Storage Wooden Mattress Beige"
    ];

    /** Collect the occurence */
    
    //$titles = array();
//    $collected_titles = array();
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
    for($x = round(count($names)/2); $x<= count($names) ; $x++){
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
        $ptitle .= $nval. " ";
    }
    echo $parent_title = rtrim($ptitle);//Final
    
    
        
    echo "<hr>";
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

 <?php ?>