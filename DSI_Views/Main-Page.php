<?php 
/** main Page - /admin.php?page=dropship-import-page */


?>



<h1 class="h1">Dropship CSV File Import</h1>
<i>Lets you update your Woocommerce Products Using CSV</i>

<hr>
<fieldset>
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
                    <input type='file' name='csv_file' id='csv_file'>
                </td>
                <td><input type='submit' id='csv_file_submit' value='Upload' accept='csv'>
                <button id='delete_all_p'>Delete All</button>
                    <div id='delete_ajxdiv'>

                    </div>
            </td>
            </tr>
        </table>
</fieldset>

<hr>

<div class='container' >
    <div class='csv-import-table-div' id='csv_ajax_table'>

    </div>
</div>

 