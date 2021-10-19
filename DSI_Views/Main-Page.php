<?php 
/** main Page - /admin.php?page=dropship-import-page */


?>



<h1 class="h1">Dropship CSV File Import</h1>
<i>Lets you update your Woocommerce Products Using CSV</i>

<hr>
<?php 
    $fieldsetclass = 'disabled';
    if(get_option('dsi_wc_ck') == '' && get_option('dsi_wc_ck') ==''){
        ?>
        <div class="update-nag notice">
            <b>WooCommerce Rest API Missing</b>
            <p>Please set the Rest API to use Dropship Import See the Instruction Below</p>
            <ol>
                <li><p>Add Key the WooCommerce Rest API in the Advanced Settings of WooCommerce  <a href="<?php _e(home_url())?>/wp-admin/admin.php?page=wc-settings&tab=advanced&section=keys" target='_BLANK'>Click This link</a>.</li>
                <li>Copy and Paste the Consumer Key and Consumer Secret Here <a href="<?php _e(home_url())?>/wp-admin/admin.php?page=dropship-import-settings#api" target='_BLANK'>Click Here</a></li>
                <li>Refresh This Page <a href="<?php _e(home_url())?>/wp-admin/admin.php?page=dropship-import-page">Click Here</a></li>
            </ol>
        </div>
        <?php    
        // check if credentials is good
        exit();
    }
    else{
        $con = new DSI_Products();
        $con->api_test_connect();

        //$get = $con->wc_api->post('products',[]);
        
        //ar_to_pre($con->wc_api);
        try{
            $con->wc_api->get('orders');
            $fieldsetclass = '';
        }
        catch( Exception $e){
            $err = $e->getMessage();
            if (strpos($err, 'key'))
                echo "Consumer Key is Invalid";

            if (strpos($err, 'signature'))
                echo "Consumer Signature is Invalid"; 
            
            
        }
        //ar_to_pre($get);
        
       
        
    }
?>

<fieldset <?php echo $fieldsetclass;?>>
    <form method='post'action="" class="" enctype='multipart/form-data'>
        <table class="table">
            <tr>
                <td><label for="dropship_company">Select Dropship Company</label></td>
                <td>
                    <select name="dropship_company" id="dropship_company">
                        <option value='aw-dropship'>AW DROPSHIP</option>
                        <option value='dropshipzone'>Dropship Zone</option>
                    </select>
                </td>
            
                <td>
                    <label for="csv_file">Select CSV File</label>
                </td>
                <td>
                    <input type='file' name='csv_file' id='csv_file''>
                </td>
                <td><input type='submit' id='csv_file_submit' value='Upload' accept='csv' class='button' >
                
                <button id='delete_all_p' class='button'>Delete All</button>
                    <div id='delete_ajxdiv'>

                    </div>
            </td>
            </tr>
        </table>
</fieldset>

<hr>
<div class='dsi-row'>
    <div class="dsi-col">
        <div class='csv-import-table-div' id='csv_ajax_table'></div>
    </div>
    <div class="dsi-col" style='min-height:400px'>
        <div class="import-result" style='padding:50px 20px 20px 20px'>
            <table class='dsi-table'   id='dsi-summary-table' style='width:500px'>
                <tr class='first-tr'>
                    <th>Sku</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
                <tbody>
                
            </table>      
        </div>
    </div>
</div>

 <?php ?>