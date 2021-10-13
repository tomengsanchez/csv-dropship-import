<h1 class="h1">Dropship CSV File Import Settings</h1>
<i>Configuration</i>

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
                            load_credential_value();
                        }
                    })
                }
                
                e.preventDefault();

            });
            
        });
        function load_credential_value(){
            
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
                    validates = [
                        jQuery('#cons_key'),
                        jQuery('#cons_secret')

                    ];
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
                    <td><input type="submit" id='submit_settings' value='Save' class='button'></td>
                </tr>
            </table>
        </form>
    </div>
    <div id='other'>
        
    </div>
</div>
<hr>
<?php 

?>
