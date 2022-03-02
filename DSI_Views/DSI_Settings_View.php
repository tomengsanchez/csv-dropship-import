<h1>Change log
</h1>
<h2>For AW-Dropshiping</h2>

<table border=1>
   <tr>
       <th>Date</th>
       <th>Type</th>
       <th>Description</th>
       <th>Investigation</th>
       <th>Solution</th>
   </tr> 
   <tr>
       <td>12.14.2021</td>
       <td>Update</td>
       <td>Configure Multilevel Category Column</td>
       <td>Need for parent-child category</td>
       <td>There is an upgraded version of category function in idropship and dropshipzone that must be implemented to aw-dropship</td>
   </tr>
   <tr>
       <td>12.14.2021</td>
       <td>Fixes</td>
       <td>3 Producst were not imported</td>
       <td>Wordpress is treating SKU Qsalt-62 and QSALT-62N the same</td>
       <td>Change the wc_product_function() to basic global[$wpdb] to get an existing SKU</td>
   </tr> 
   <tr>
       <td>1.19.2022</td>
       <td>Change Request</td>
       <td>Use UNIT RRP column instead of Unit Price</td>
       <td>Wordpress is treating SKU Qsalt-62 and QSALT-62N the same</td>
       <td>Change the wc_product_function() to basic global[$wpdb] to get an existing SKU</td>
   </tr> 
</table>
<hr>
<h2>For idropship </h2>
<table border=1>
   <tr>
       <th>Date</th>
       <th>Type</th>
       <th>Description</th>
       <th>Investigation</th>
       <th>Solution</th>
   </tr> 
</table>
<hr>
<h2>For Dropshipzone </h2>
<table border=1>
   <tr>
       <th>Date</th>
       <th>Type</th>
       <th>Description</th>
       <th>Investigation</th>
       <th>Solution</th>
   </tr>
   <td>12.14.2021</td>
       <td>Bugsfound(Not Fixed)</td>
       <td>Description Data too long. It fails the http request</td>
       <td>I put the description data to other dropshiping and it has the same issue</td>
       <td>Compress maybe</td>
    </tr>
    <td>1.19.2022</td>
       <td>Re Assign the column</td>
       <td>Starting csv was different from the requested csv</td>
       <td>I put the description data to other dropshiping and it has the same issue</td>
       <td>Compress maybe</td>
    </tr>
</table>