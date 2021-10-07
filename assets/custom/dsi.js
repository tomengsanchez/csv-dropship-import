/**
 * Custom Javascript for CSV DropShip Imports Page : page=dropship-import-page
 */

jQuery(document).ready(function(){
    //jQuery('.upload_control_group').controlgroup();
    jQuery('#csv_file_submit').click(function(e){
        e.preventDefault();
        
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
            data:form_data,
            
            success:function(res){
                jQuery('#csv_ajax_table').html(res);
                // if(!res.message)
                //     jQuery('#csv_ajax_table').html('12');
                // else
                //     jQuery('#csv_ajax_table').html(span_parser_alert(res.message));
            }   
        });
    });
    
});
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
                
            }
        });
    });
});


/**
 * Custom Javascript for CSV DropShip Imports 
 * Page : page=dropship-providers 
 * File : DSI_Dropship_Providers_Views
 * 
 * 
 */
