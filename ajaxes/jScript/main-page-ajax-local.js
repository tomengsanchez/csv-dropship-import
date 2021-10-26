jQuery(document).ready(function(){
    jQuery('#start_import').click(function(e){
        e.preventDefault();
        //selected_category_column = jQuery(this).parent().siblings('td.td_select').children('select.select_category_column').val();
        mark_up_price_base = jQuery(this).parent().parent().siblings('td.price_mark_up_td').children('select.price_mark_up_select').val();
        mark_up_price_base = jQuery(this).parent().parent().siblings().children('td.price_mark_up_td').children('select.price_mark_up_select').val();
        mark_up_price_value = jQuery(this).parent().parent().siblings().children('td.price_mark_up_value_td').children('input#price_mark_up_text').val();
        upload_images_c = jQuery(this).parent().parent().siblings().children('td.upload_images_td').children('input#upload_images').is(':checked');
        sel = jQuery(this).parent().siblings('td.td_select').children('select.select_category_column').val();
        if(jQuery('select.price_mark_up_select').val()=='None'){
            read_rows_from_table(jQuery(this).siblings('table'),sel,mark_up_price_base,mark_up_price_value,upload_images_c);    
        }
        else{
            if(mark_up_price_value ==''){
                alert('Please Input Number');
                jQuery('input#price_mark_up_text').focus();
            }
            else{
                if(validate_number(jQuery('input#price_mark_up_text'))){
                    
                    read_rows_from_table(jQuery(this).siblings('table'),sel,mark_up_price_base,mark_up_price_value,upload_images_c);    
                }
                else{
                    alert('Mark Up Price is not a Number');
                    jQuery('input#price_mark_up_text').focus();
                }
            }
        }
    }).addClass('button').animate('1000');
    jQuery('select.price_mark_up_select').change(function(){
        //console.log(jQuery(this).val());
        if(jQuery(this).val() == 'None'){
            jQuery('input#price_mark_up_text').attr('disabled','disabled');
        }
        else{
            jQuery('input#price_mark_up_text').removeAttr('disabled');
        }
    });
});
