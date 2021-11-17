<div id="settings-tabs">
    <ul>

        <li><a href="#api">WooCommerce API settings</a></li>
        <li><a href="#other">Other Settings</a></li>
    </ul>
    <script>
        jQuery(document).ready(function(){
            load_credential_value();
            jQuery('#form-api-settings').on('submit',function(e){
                var datastring = jQuery(this).serialize();
                validates = [
                    jQuery('#cons_key'),
                    jQuery('#cons_secret')

                ];
                if(validate_these(validates)){
                    jQuery.ajax({
                        method:'POST',
                        data:{
                            _nonce : locsData.csv_nonce,
                            ck : jQuery('#cons_key').val(),
                            cs : jQuery('#cons_secret').val()
                        },
                        url:locsData.admin_url + 'admin-ajax.php?action=set_credentials',
                        success:function(e){
                            jQuery('#alert').html(e.message);
                            load_credential_value();
                            
                        }
                    })
                }
                e.preventDefault();
            });
            
        });
        function load_credential_value(){
            validates = [
                jQuery('#cons_key'),
                jQuery('#cons_secret')

            ];
            jQuery.ajax({
                method:'POST',
                data:{
                    _nonce : locsData.csv_nonce
                },
                url:locsData.admin_url + 'admin-ajax.php?action=get_dsi_api_credentials',
                success:function(e){
                    //alert(e);
                    jQuery('#cons_key').val((e.ck)?e.ck:'');
                    jQuery('#cons_secret').val((e.cs)?e.cs:'');
                    
                    
                    validate_these(validates);
                }
            });
           
        }
    </script>
    
    <div id='api' class='tabs'>
        <form id='form-api-settings' action="<?php echo admin_url('admin.php?page=dropship-import-settings');?>" method='POST'>
            <table>
                <tr>
                    <td>URL</td>
                    <td><input type="text" id='url' name='cons_key' value='<?php echo home_url()?>' readonly></td>
                </tr>
                <tr>
                    <td>Consumer Key</td>
                    <td><input type="text" id='cons_key' name='cons_key' val_message='Please Enter Consumer Key'></td>
                </tr>
                <tr>
                    <td>Consumer Secret</td>
                    <td><input type="text" id='cons_secret' name='cons_secret' val_message='Please Enter Consumer Key'></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" id='submit_settings' value='Save' class='button'><div id='alert'></div></td>
                </tr>
            </table>
        </form>
        <hr>
        <h2>Instructions</h2>
        <ol>
            <li><p>Add Key the WooCommerce Rest API in the Advanced Settings of WooCommerce  <a href="<?php _e(home_url())?>/wp-admin/admin.php?page=wc-settings&tab=advanced&section=keys" target='_BLANK'>Click This link</a>.</li>
            <li>Copy and Paste the Consumer Key and Consumer Secret</li>
        </ol>
        <img  src="<?php echo plugins_url('csv-dropship-import/assets/img/dsi_wc_api_ss.png')?>" alt="" height='400px'>
    </div>
    
    <div id='other'>
        
    </div>
</div>
<hr>
<?php 

?>
