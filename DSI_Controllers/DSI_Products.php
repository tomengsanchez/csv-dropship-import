<?php 
/**
 * This is the Classes for the Products
 * 
 * @param array $collect_meta_to_import - collect all the final meta/field before import
 * 
 * 
 * 
 */
class DSI_Products extends DSI_Loader{
    /** product information  */
    /** collect all the final meta/field before import */

    var $products = array();
    var $collect_meta_to_import = array();
    var $data_per_lines = array();

    var $product_type = '';
    var $product_id = '';
    var $product_type_others = array(
        'default' => 'simple'
    );
    var $dropship_company = array();

    var $image = array();

    
    public function __construct(){
        
    }
    
    function get_all_wc_default_field(){
        $default_field = array(
            'post_title',
            'post_content',
            'post_status',
            'post_type,',
            'product_type',
            '_visibility',
            'total_sales',
            '_downloadable',
            '_virtual',
            '_sale_price',
            '_regular_price',
            '_featured',
            '_weight',
            '_length',
            '_width',
            '_height',
            '_sku',
            '_product_attributes',
            '_sale_price_dates_from',
            '_sale_price_dtes_to',
            '_price',
            '_sold_individually',
            '_backorders',
            '_stock',
        );
        return $default_field;
    }

    /**
     * Set All the Default Field of Wcommerce product
     * @param array $field_collection -- collects the array of the desired field first 5 param should be 'post_title','post_content','post_status','post_type,','product_type'
     * 
     */
    function set_meta_to_import($field_collection = array()){
        if(!$field_collection){
            $this->collect_meta_to_import = $this->get_all_wc_default_field();
        }
        else{
            $this->collect_meta_to_import = $field_collection;
        }
    }
    /**
     * Assigns the product meta field to the specific solumn of the csv
     * @param array $data - array of metafield and converted meta field format from csv headingarray('meta_field','_')
     * @param array $heading array of the heading of the csv file
     * 
     * @return array $assigned_field_column collection of assigned field column
     * )
     * 
     */
    function ds_assign_column($data,$heading){
        $assigned_field_column = array();
        $x = 0;
        foreach ($data as $d){
            $column_number = 0;// get The Column Number of the Assigned Converted Heading
            for($i = 0; $i< count($heading); $i++ ){
                if(conv_string_to_meta( $heading[$i]) == $d[1]){ // Check if converted headings of assigned and converted csv headings are the same
                    $column_number = $i;//assig for column number
                    //echo $heading[$i];
                }
            }
            // will make the assigned colum and columng number to search
            array_push($assigned_field_column,array($d[0],$column_number));
            $x++;
        }
        return $assigned_field_column;
    }
    /**
     * Read the file please use fo() and this function will set $data_per_lines to each lines
     * 
     * @param file file data $_FILE['name']['tmpname']
     */ 
    function read_csv_lines($file){
        while(!feof($file)){
            $lines = fgetcsv($file);
            array_push($this->data_per_lines,$lines);
        }
    }
    /**
     * Import CSV to Woocommerce Products
     *  
     * @param array $column_assignment - collection of default_column and the csv_column number example : 'post_title'=> [10]
     * @param array $lines csv_files columns and rows
     * 
     * 
     */
    function import_csv_to_wc($column_assigment= array(),$lines = array()){    
        //echo count($lines);
        for($x = 0; $x < count($lines); $x++){//loop each line
            //loop each field
            echo "Impor these line " . $x;

            echo "<br>";
            echo "<table border=1>";
            echo "<tr>";
            
            echo "<th>Index</th>";
            echo "<th>Meta VAlue</th>";
            echo "<th>Csv Column Number</th>";
            echo "<th>Meta Value</th>";
            echo "</tr>";
            for($i = 0; $i < count($column_assigment);$i++ ){// loop each field

                echo "<tr>";    
                echo "<td> " . $i. "</td>";
                echo "<td> " . $column_assigment[$i][0]. "</td>";
                echo "<td> " . $column_assigment[$i][1]. "</td>";

                $val = '';
                if($column_assigment[$i][1] == 0){
                    $val = 'Please Add Another meta field using : ' . $column_assigment[$i][0];
                    
                }   
                else
                    
                    $val = $lines[$x][$column_assigment[$i][1]];
                    if($i == 4){
                        $val = $this->product_type_others['default'];
                    }
                echo "<td>";
                    echo $val;
                echo "</td>";
                echo "</tr>";    
            }
            echo "</table>";
        }
    }
    /**
     * 
     */
}
?>