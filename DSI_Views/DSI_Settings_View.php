<h1 class="h1">Dropship CSV File Import Settings</h1>
<i>Configuration</i>

<div id="settings-tabs">
    <ul>

        <li><a href="#api">WooCommerce API settings</a></li>
        <li><a href="#other">Other Settings</a></li>
    </ul>
    <script>
        jQuery(document).ready(function(){
            jQuery('#form-api-settings').on('submit',function(e){
                alert(1);
                e.preventDefault();
            });
        });
    </script>
    <div id='api' class='tabs'>
        <form id='form-api-settings' action="<?php echo admin_url('admin.php?page=dropship-import-settings');?>" method='POST'>
            <table>
                <tr>
                    <td>URL</td>
                    <td><input type="text" id='cons_key' name='cons_key' disabled value='<?php echo home_url()?>'></td>
                </tr>
                <tr>
                    <td>Consumer Key</td>
                    <td><input type="text" id='cons_key' name='cons_key'></td>
                </tr>
                <tr>
                    <td>Consumer Secret</td>
                    <td><input type="text" id='cons_secret' name='cons_secret'></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" id='submit_settings' value='Save' class='button'></td>
                </tr>
            </table>
        </form>
    </div>
    <div id='other'>

    </div>
</div>
<hr>

