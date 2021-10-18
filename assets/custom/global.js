/**
 * Inline Table format json datashould be this ->    maptable: {First: "2nd Column"}
 */
jQuery(document).ready(function(){

}
);
 function inline_form_table_json(data,container){
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
        output += '<td class="product-values wrap" csv-row-number="'+ data.row[r].split('wci_split')[1] + '">' + data.row[r].split('wci_split')[0]+ '['+ data.row[r].split('wci_split')[1]+ ']</td>';
        output += '<td class="product-values wrap" csv-row-number="'+ data.row[r].split('wci_split')[3] + '">' + data.row[r].split('wci_split')[2]+ '</td>';
        output += '</tr>';
    }
    output += '</tbody>';
    output += '</table>';
    container.html(output);
}

function json_script_p(json){
    return '<script>'+ json +'</script>';
}

function read_rows_from_table(x){
    //get Table data
    var tbodyElement = x.children('tbody');
    var tr = tbodyElement.children('tr');
    tr.children('td').each(function(){

        alert(jQuery(this).html());

    });
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

    
}