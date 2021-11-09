/**
 * Inline Table format json datashould be this ->    maptable: {First: "2nd Column"}
 */
jQuery(document).ready(function(){
    
}
);
var data_per_lines = Array();
var variation_parents = Array();
var categories = Array();

 function inline_form_table_json(data,container){
    jQuery('.select_category').selectmenu();
    variation_parents = data.variation_parents;    
    data_per_lines = data.data_per_lines;
    
    if(data_per_lines && data.valid == true){
        jQuery('#row_holder_finish').val(data_per_lines.length);
        categories = data.categories;
        var output = '';

        opt = '';
        cat_ctr = 0;
        for(var r in data.row){
            
            col_n = data.row[r].split('wci_split')[1] * 1;
            // if(col_n == 3){
            //     opt += '<option value="' + data.row[r].split('wci_split')[1] + '">'+ data.row[r].split('wci_split')[0] +'</option>';
            // }
            // else{
                if(jQuery('#dropship_company').val() == 'aw-dropship'){
                    if(col_n > 30){
                        col_head = data.row[r].split('wci_split')[0];
                        if(col_head != ''){
                            opt += '<option value="' + data.row[r].split('wci_split')[1] + '">'+ data.row[r].split('wci_split')[0] +'</option>';
                        }
                    }
                }
                if(jQuery('#dropship_company').val() == 'idropship'){
                    if(col_n == 26){
                        col_head = data.row[r].split('wci_split')[0];
                        if(col_head != ''){
                            opt += '<option value="' + data.row[r].split('wci_split')[1] + '">'+ data.row[r].split('wci_split')[0] +'</option>';
                        }
                    }
                }
                
        }
        output += '<table>';
        output += '<tr>';
        output += '<td><span style="padding:0px 0px 0px 20px;">Skip Existing SKU</span></td>';
        output += '<td class="skip_existing_sku_td"><input type="checkbox" checked="checked" id="skip_existing_sku"></td>';
        output += '<td></td>';
        output += '</tr>';
        output += '<tr>';
        output += '<td><span style="padding:0px 0px 0px 20px;">Upload/Update Images</span>';
        output += '<td class="upload_images_td"><input type="checkbox" checked="checked" id="upload_images"></td>';
        output += '<td></td>';
        output += '</tr>';
        output += '<tr><td><span style="padding:0px 0px 0px 20px;" colspan="2">Price Mark-Up</span></td><td class="price_mark_up_td"><select class="price_mark_up_select"><option>None</option><option>Price $</option><option>Percentage %</option></select></td>';
        output += '<td class="price_mark_up_value_td"><input type="text" id="price_mark_up_text" disabled size="10"></td>';
        output += '</tr>';
        output += '<tr><td><span style="padding:0px 0px 0px 20px;">Category Column : </span></td><td class="td_select"><select class="select select_category_column">' + opt + '</select></td>';
        output += '<td></tr>';
        output += '<tr>';
        output += '<td></td>';
        output += '<td><button id="start_import" class"button">Start Import</button></td></td>';
        output += '</tr>';
        output += '</table>';
        output += '<table class="dsi-table" id="#csv-field">';
        output += '<thead class="dsi-thead">';
        output += '<tr >';
        output += '<th width=100px>Product Field</th>';
        output += '<th >CSV Column[Col#]</th>';
        output += '<th >CSV Values</th>';
        output += '</thead>';
        output += '<tbody class="dsi-tbody">';
        
        for(var r in data.row){
            //output += r + '->' + data.row[r] + "<br>";
            opt += '<option>'+ data.row[r].split('wci_split')[0] +'</option>';
            output += '<tr >';
            output += '<td class="product-field" field-name="'+ r +'"><b>' + r + '</b></td>';
            output += '<td class="product-col wrap" csv-row-numbers="'+ data.row[r].split('wci_split')[1] + '">' + data.row[r].split('wci_split')[0]+ '['+ data.row[r].split('wci_split')[1]+ ']</td>';
            output += "<td style='word-wrap:break-word' class='product-values wrap' csv-row-values='" + data.row[r].split('wci_split')[3] + "'>" + jQuery.trim(data.row[r].split('wci_split')[2]) + "</td>";
            output += '</tr>';
        }
        output += '</tbody>';
        output += '</table>';

        //output += json_script_p(data.script);
        container.html(output);
    }
    else{
        jQuery('#csv_ajax_table').html('<span style="padding-left:30px">Please Select the Correct .CSV File For ' + jQuery('#dropship_company option:selected').html() +'</span>');
    }
}
function json_script_p(json){
    return '<script src="' + json+ '"></script>';
}


field_names =Array();
field_values = Array();
csv_columns = Array();
var upload_images_ = '';
var mark_up_base_ = '';
var mark_up_value_ = '';
/**
 * Read Rows From TAble
 * @param {x} x 
 */
function read_rows_from_table(x,selected_category,mark_up_base,mark_up_value,upload_images_a,skip_existing_sku_c){
    //get Table data
    mark_up_base_ = mark_up_base;
    mark_up_value_ = mark_up_value;
    upload_images_ = upload_images_a;
    skip_existing_sku_ = skip_existing_sku_c;
    
    selected_category_column = selected_category;
    
    var tbodyElement = x.children('tbody');
    var tr = tbodyElement.children('tr');
    
    tr.children('td.product-field').each(function(){
        field_names.push(jQuery(this).attr('field-name'));
        field_values.push(jQuery(this).siblings('td.product-values').attr('csv-row-values'));
        csv_columns.push(jQuery(this).siblings('td.product-col').attr('csv-row-numbers'));
    });
    /**
     * Sends data to ajax from the collected table data and values from the server
     * 
     * @param array      data_per_lines values of lines per column
     * @param string     selected_category values of the selectec category column
     * @param string mark_base selected markup price
     * @param numeric mark_value value of the selected mark up price
     */
    sends_data_to_ajax(data_per_lines,selected_category_column,mark_up_base_,mark_up_value_,upload_images_);
    
    //jQuery.fn.alwayspogi('yesyes');

    /** task for tomorrow, 
     * design table first
     * make class for each td of everyrows. 
     * collect them to array
     * send each collected array to ajax url
     * append each on the table
     * prepare update script
     */
    //read tbody
    //loop tbody

    jQuery('.add-row').remove();
    jQuery('.add-row').remove();
    jQuery('.import_files').html('0');
    
}
const ctr =1;
function sends_data_to_ajax(){
    jQuery('#dsi-summary-table').append("<tr class='tr-please-wait'><td colspan='4'><h3>Please Wait...</h3></td></tr>");
    jQuery('.read_files').html(data_per_lines.length);
    
    var b =1;
    jQuery('#row_holder_start').val(0 * 1);
    //console.log(data_per_lines);
    send_one_by_one_ajax();
}
var selected_category_column;
function send_one_by_one_ajax(){
    
    variation_parents_ = variation_parents;

    if(jQuery('#dropship_company').val()== 'aw-dropship'){
        url_ = locsData.admin_url+'admin-ajax.php?action=get_field_then_import';
    }
    else{
        url_ = locsData.admin_url+'admin-ajax.php?action=get_field_then_import_idropship';
    }
    trout = '';

    
    aj = jQuery.ajax({
        url:url_,
        type:'POST',
        start_time:new Date().getTime(),
        data : {
            lines : data_per_lines[jQuery('#row_holder_start').val() *1],
            names : field_names,
            values : field_values,
            csv_columns : csv_columns,
            selected_category : selected_category_column,
            mark_up_base: mark_up_base_,
            mark_up_value: mark_up_value_,
            upload_images_yes: upload_images_,
            skip_existing_sku_yes : skip_existing_sku_,
            variation_parents_ :variation_parents
            
        
        }
    }).always(function(e){
        start= jQuery('#row_holder_start').val() *1;
        finish = jQuery('#row_holder_finish').val() *1;
        if(start >= (finish-1)){
            //jQuery('#row_holder_start').val(0 * 1);
            
        }
        else{
            jQuery('#row_holder_start').val((jQuery('#row_holder_start').val() *1)+1); 
            send_one_by_one_ajax();
            //alert(data_per_lines[(jQuery('#row_holder_start').val() *1)+1]);
            var imported_files = 0;
        }
        imported_files = jQuery('.import_files').html();
        jQuery('.progress').html(('0'));
        jQuery('.import_files').html((imported_files*1)+ 1);
        $dvdn = jQuery('.read_files').html() * 1;
        $dvsr = jQuery('.import_files').html() * 1;
        $percentege = ($dvsr/$dvdn)*100;
        $percentege = $percentege.toFixed(0);
        //jQuery('.progress').html(($percentege));
        jQuery( "#progressbar" ).progressbar({
            value : $percentege * 1
        });
        // if(e.status_message=='Skipped'){
        //     jQuery('.tr-please-wait').remove();
        //     jQuery('#dsi-summary-table').append("<tr class='tr-please-wait'><td colspan='4'><h3>Please Wait while skipping lines</h3></td></tr>");

        // }else{
            t = new Date().getTime() - this.start_time;
            
            trout+='<tr>';
            trout+='<td>' + e.data.sku + "</td>";
            trout+='<td>' + e.data.name + "</td>";
            trout+='<td>' + e.data.price + "</td>";
            trout+='<td>111' + e.status_message + '('+ new Date().getTime() - this.start_time +' ms)</td>';
            trout+='</tr>';
            jQuery('#dsi-summary-table').append("<tr class='add-row'><td>" + e.data.sku + "</td><td>" + e.data.name + "</td><td>" + e.data.price + "</td><td class='res' status='" + e.status_message +"'>" + e.status_message + " (" + e.loading_time +" s/ " + ((t/1000).toFixed(2)) + "s)</td></tr>");
            jQuery('.tr-please-wait').remove();
        // }
        if($dvdn == $dvsr){
            jQuery('#start_import').removeAttr('disabled');
            jQuery('select.price_mark_up_select').removeAttr('disabled','disabled');
            jQuery('input#price_mark_up_text').removeAttr('disabled','disabled');
            jQuery('input#upload_images').removeAttr('disabled','disabled');
            jQuery("#skip_existing_sku").removeAttr('disabled','disabled');
            jQuery('select.select_category_column').removeAttr('disabled','disabled');
            if(jQuery('select.price_mark_up_select').val() == 'None'){
                jQuery('input#price_mark_up_text').attr('disabled','disabled');
            }
            else{
                jQuery('input#price_mark_up_text').removeAttr('disabled');
            }
        }
        
            
    }).fail(function(){
        trout+='<tr>';
        trout+='<td>' + e.data.sku + "</td>";
        trout+='<td>' + e.data.name + "</td>";
        trout+='<td>' + e.data.price + "</td>";
        trout+='<td>' + e.status_message + "</td>";
        trout+='</tr>';
        jQuery('#dsi-summary-table').append("<tr class='add-row' colspan='4'><b>Import Error!!!</b><td></td></tr>");
    });
}
function get_selected_column(selected){
    selected_category_column = selected;
    alert(selected);
}