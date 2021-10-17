/**
 * Inline Table format json datashould be this ->    maptable: {First: "2nd Column"}
 */
 function inline_form_table_json(data,container){
    var output= '';
    output += '<table class="dsi-table" id="#csv-field">';
    output += '<thead class="dsi-thead">';
    output += '<tr >';
    output += '<th width=100px>Product Field</th>';
    output += '<th >CSV Column</th>';
    output += '</thead>';
    output += '<tbody class="dsi-tbody">';
    for(var r in data.row){
        //output += r + '->' + data.row[r] + "<br>";

        output += '<tr >';
        output += '<td ><b>' + r + '</b></td>';
        output += '<td >' + data.row[r].split('-')[0]+ '</td>';
        output += '</tr>';
    }
    output += '</tbody>';
    output += '</table>';
    container.html(output);


}