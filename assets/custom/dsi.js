/**
 * Custom Javascript for CSV DropShip Imports Page : page=dropship-import-page
 */

 var nameReg = /^[A-Za-z]+$/;
 var numberReg =  /^[0-9]+$/;
 var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;


 function validate_this(input_obj, reg_test){
    input_obj.next('span').remove();
    if(input_obj.val() == ""){
        input_obj.after('<span class="error">'  + input_obj.attr('val_message') +'</span>');
        return false;
    }
    else{
        return true;
    }
     
 }
 
 var numberRegex = /^\s*[+-]?(\d+|\.\d+|\d+\.\d+|\d+\.)(e[+-]?\d+)?\s*$/;
function validate_number(num){
    
    return(numberRegex.test(num.val()));
}

 
 function validate_these(input_ojects){
    val_counter = 0;
    exit();
    jQuery(input_ojects).each(function(){
        if(validate_this(this)==false){
            val_counter++;
        }
    });
    if(val_counter == 0)
        return true;
    else  
        return false;
 }

jQuery(document).ready(function(){
    //jQuery('.upload_control_group').controlgroup();
    jQuery('#dropship_company').change(function(){
        jQuery('#csv_file').trigger('change');
    });
    jQuery('#csv_file').change(function(e){
        line_counter = 0;
        e.preventDefault();
        //reset import status parameter
        
        
        jQuery('.add-row').remove();
        jQuery( "#progressbar" ).progressbar({value:0});
        jQuery('.import_files').html(0);
        jQuery('.read_files').html(0);
        // jQuery('#csv_ajax_table').html('<img class="loading-small" src="' + locsData.home_url + '/wp-content/plugins/csv-dropship-import/assets/img/loading.png")">');
        jQuery('#csv_ajax_table').html('Please Wait');
        var file_data = jQuery("#csv_file")[0].files[0]; //Get the File Input
        var form_data = new FormData(); // prepare form ddata
        form_data.append("csv_file",file_data); // Collect Form Data from the Inputs
        form_data.append("_nonce",locsData.csv_nonce); // Collect Form Data from the Inputs
        form_data.append("message",'Hello'); // Collect Form Data from the Inputs
        form_data.append("dropship_company",jQuery("#dropship_company").val()); // Collect Form Data from the Inputs
        jQuery.ajax({
            url:locsData.admin_url+'admin-ajax.php?action=upload_csv_files',
            type:'POST',
            //dataType:'json',
            contentType:false,
            processData: false,
            cache:false,
            data:form_data,
            
            success:function(res,s){
                //alert(res);
                
                jQuery('#csv_ajax_table').html('Select Dropship Supplier');

                if(jQuery('#dropship_company').val()=='aw-dropship'){
                    inline_form_table_json(res,jQuery('#csv_ajax_table'));
                    // ajax the script
                    jQuery.ajax({
                        url:locsData.admin_url+'admin-ajax.php?action=get_ajax_script_main_page',
                        cache:false,
                        success:function(ajs){
                            // ajs = ajs.replace(/\s/g, '');
                            jQuery('#csv_ajax_table').append(ajs);
                        }
                    });
                }
                if(jQuery('#dropship_company').val()=='idropship'){
                    
                    inline_form_table_json(res,jQuery('#csv_ajax_table'));
                    jQuery.ajax({
                        url:locsData.admin_url+'admin-ajax.php?action=get_ajax_script_main_page',
                        cache:false,
                        success:function(ajs){
                            // ajs = ajs.replace(/\s/g, '');
                            jQuery('#csv_ajax_table').before(ajs);
                            //jQuery('#csv_ajax_table').append(ajs);
                        }
                    });
                }
                if(jQuery('#dropship_company').val()=='dropshipzone'){
                    
                    inline_form_table_json(res,jQuery('#csv_ajax_table'));
                    jQuery.ajax({
                        url:locsData.admin_url+'admin-ajax.php?action=get_ajax_script_main_page',
                        cache:false,
                        success:function(ajs){
                            // ajs = ajs.replace(/\s/g, '');
                            jQuery('#csv_ajax_table').before(ajs);
                            //jQuery('#csv_ajax_table').append(ajs);
                        }
                    });
                }
                // if(!res.message)
                //     jQuery('#csv_ajax_table').html('12');
                // else
                //     jQuery('#csv_ajax_table').html(span_parser_alert(res.message));
            }   
        });
        
    });
    
    jQuery('#start_import').click(function(e){
        e.preventDefault();
        table_div = jQuery('#csv_ajax_table').html();
        jQuery('.test').html(table_div);

    });
});// ready
//json Parse to HTML
function span_parser_alert(jsonData){
    return "<span class='span_parser_alert'>" + jsonData+ "</span>";
}
    

/**
 * Custom Javascript for CSV DropShip Imports 
 * Page : page=dropship-providers 
 * File : DSI_Dropship_Providers_Views
 * 
 * 
 */

jQuery(document).ready(function(){
    jQuery('#test_product_import').click(function(){
        jQuery.ajax({
            type : "POST",
            url:locsData.admin_url+'admin-ajax.php?action=test_product_import',
            data:{
                _nonce : locsData.csv_nonce
            },
            success:function(r){
                jQuery('#test_product_import_ajdiv').html(r);
            }
        });
        
    });

    jQuery("#delete_all_p").click(function(e){
        e.preventDefault();
        //alert(1);
        jQuery.ajax({
            type:"POST",
            url:locsData.admin_url + 'admin-ajax.php?action=delete_all_products',
            data:{
                _nonce : locsData.csv_nonce
            },
            success:function(r){
                //alert(r);
                //jQuery('#test_product_import_ajdiv').html(r);
                jQuery('#csv_ajax_table').html(r);
                jQuery('.add-row').remove();
                jQuery('.add-row').remove();
                jQuery('.import_files').html('0');
                
            }
        });
    });
});




/**
 * Custom Javascript for Settings
 * PAGE : page=dropship-import-settings
 * File : DSI_Settings_View.php
 */

jQuery(document).ready(function(){
    jQuery("#settings-tabs").tabs();
});





