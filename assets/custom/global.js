/**
 * Inline Table format json datashould be this ->    maptable: {First: "2nd Column"}
 */
jQuery(document).ready(function(){

}
);
var data_per_lines = Array();
var categories = Array();
 function inline_form_table_json(data,container){
    data_per_lines = data.data_per_lines;
    jQuery('#row_holder_finish').val(data_per_lines.length);
    categories = data.categories;
    var output= json_script_p(data.script);
    output += '<button id="start_import" class"button">Start Import</button>';
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
        output += '<tr >';
        output += '<td class="product-field" field-name="'+ r +'"><b>' + r + '</b></td>';
        output += '<td class="product-col wrap" csv-row-numbers="'+ data.row[r].split('wci_split')[1] + '">' + data.row[r].split('wci_split')[0]+ '['+ data.row[r].split('wci_split')[1]+ ']</td>';
        output += '<td class="product-values wrap" csv-row-values="'+ data.row[r].split('wci_split')[3] + '">' + data.row[r].split('wci_split')[2]+ '</td>';
        output += '</tr>';
    }
    output += '</tbody>';
    output += '</table>';
    container.html(output);
}

function json_script_p(json){
    return '<script>'+ json +'</script>';
}


field_names =Array();
field_values = Array();
csv_columns = Array();

/**
 * Read Rows From TAble
 * @param {x} x 
 */
function read_rows_from_table(x){
    //get Table data
    var tbodyElement = x.children('tbody');
    var tr = tbodyElement.children('tr');
    
    tr.children('td.product-field').each(function(){
        field_names.push(jQuery(this).attr('field-name'));
        field_values.push(jQuery(this).siblings('td.product-values').attr('csv-row-values'));
        csv_columns.push(jQuery(this).siblings('td.product-col').attr('csv-row-numbers'));
    });

    sends_data_to_ajax(data_per_lines);
    
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
    for(x = 0; x < data_per_lines.length ;x++){
        
        // console.log(data_per_lines[x]);
        aj = jQuery.ajax({
            url:locsData.admin_url+'admin-ajax.php?action=get_field_then_import',
            type:'POST',
            data : {
                lines : data_per_lines[x],
                names : field_names,
                values : field_values,
                csv_columns : csv_columns,
                category:categories,
                row_counter: x
                
            },
            beforeSend :function(){
                //alert(1);
                
            }
            
        }).done(function(){
            
        }).always(function(e,stat){
            trout= '';
            //console.log(stat);        
            
            if(stat=='success'){
                var imported_files = 0;
                jQuery('.progress').html(('0'));
                imported_files = jQuery('.import_files').html();
                jQuery('.import_files').html((imported_files*1)+ 1);

                
                    $dvdn = jQuery('.read_files').html() * 1;
                    $dvsr = jQuery('.import_files').html() * 1;
                    $percentege = ($dvsr/$dvdn)*100;
                    $percentege = $percentege.toFixed(0);
                    //jQuery('.progress').html(($percentege));
                    jQuery( "#progressbar" ).progressbar({
                        value : $percentege * 1
                    });
                

                
                // trout+='<tr>';
                // trout+='<td>' + e.data.sku + "</td>";
                // trout+='<td>' + e.data.name + "</td>";
                // trout+='<td>' + e.data.price + "</td>";
                // trout+='<td>' + e.status_message + "</td>";
                // trout+='</tr>';
                jQuery('#dsi-summary-table').append("<tr class='add-row'><td>" + e.data.sku + "</td><td>" + e.data.name + "</td><td>" + e.data.price + "</td><td class='res' status='" + e.status_message +"'>" + e.status_message + "</td></tr>");
                jQuery('.tr-please-wait').remove();
                
                //ctr = ctr+ 1;
                b++;
            }else{
                jQuery('#dsi-summary-table').append("<tr class='add-row'><td>" + e.data.sku + "</td><td>" + e.data.name + "</td><td>" + e.data.price + "</td><td>ERROR</td></tr>");
            }
            trout= '';
            
        }).fail(function(e){
            jQuery('#dsi-summary-table').append("<tr class='add-row'><td>" + e.data.sku + "</td><td>" + e.data.name + "</td><td>" + e.data.price + "</td><td>ERROR</td></tr>");
        });
        console.log(aj);
    }
    


    // jQuery(data_per_lines).each(function(){
    //     trout= '';
    //     jQuery.ajax({
    //         url:locsData.admin_url+'admin-ajax.php?action=get_field_then_import',
    //         type:'POST',
    //         data : {
    //             lines : this,
    //             names : field_names,
    //             values : field_values,
    //             csv_columns : csv_columns
    //         }
            
    //     }).done(function(e,stat){
    //         console.log(stat);        
    //         if(stat=='success'){
    //             trout+='<tr>';
    //             trout+='<td>' + e.data.sku + "</td>";
    //             trout+='<td>' + e.data.name + "</td>";
    //             trout+='<td>' + e.data.price + "</td>";
    //             trout+='<td>' + e.status_message + "</td>";
    //             trout+='</tr>';
    //             jQuery('#dsi-summary-table tbody').append(trout);
    //             ctr = ctr+ 1;
    //         }
            
            
    //     });
        
    // });

    //console.log(ctr);
    
}

