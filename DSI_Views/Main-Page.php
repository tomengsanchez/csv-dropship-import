<?php 
/** main Page - /admin.php?page=dropship-import-page */


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
                        <option value='dropshipzone'>Dropship Zone</option>
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
                    <div id='delete_ajxdiv'>

                    </div>
            </td>
            </tr>
        </table>
</fieldset>

<hr>
    <?php 
      
        
    ?>

<div class='dsi-row'>
    <input type='hidden' class='loop-counter' value='0'>
    <div class="dsi-col">
        <div class='csv-import-table-div' id='csv_ajax_table'></div>
    </div>
    <div class="dsi-col" style='min-height:400px'>
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
        <div class="import-result" >
            <!-- <input type='text' id='row_holder_finish'>
            <input type='text' id='row_holder_start' value='0'> -->
            <table width='600'>
                <tr>
                    <td width='200'><h3 style='padding-left:12px'><span class='import_files'>0</span> of <span class='read_files'>0</span > are imported</h3></td>
                    <td width='100'><h3><b>Progress : </b></h3></td>
                    <td ><div style='width:200px' id='progressbar'></div></td>
                </tr>
            </table>
            <table class=''   id='dsi-summary-table' style='width:600px;background-color:white'>
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