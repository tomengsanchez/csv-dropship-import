<?php 
/**
 * Custom Functions
 */

function ar_to_pre($arr=array()){
    echo "<pre>";
    print_r($arr);
    echo  "</pre>";
    
}
/**
 * Covert CSV HEADING TO META FIELD FORMAT
 * 
 * @param array $array_heading array of heading to be formatted
 * 
 */
function conv_csv_heading_to_meta($array_heading){
    $formatted_heading = array();

    for($i = 0; $i < count($array_heading); $i++){
        $format = conv_string_to_meta($array_heading[$i]);
        array_push($formatted_heading,$format);
    }
    ar_to_pre($formatted_heading);
}
/**
 * convert Regular String to Meta
 * 
 * @param string $string Strings to Convert
 */
function conv_string_to_meta($string){
    $format = str_replace(' ','_',$string);// " " to _
    $format = str_replace('(','',$format);// "(" to ""
    $format = str_replace(')','',$format);// ")" to ""
    $format = str_replace('/','_',$format);// ")" to ""
    $format = strtolower($format);
    return "_" . $format;
}

?>