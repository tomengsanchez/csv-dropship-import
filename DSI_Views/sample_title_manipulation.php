<?php 

// Objectives ...Remove the attribute from the each names to make the parent_title.
    // Use these 3 sample scenarios of names
    // Parent for 1. 'DreamZ Fitted Waterproof Mattres Protectr with Ramboo Filbre Cover'
    $parent_title = '';

    $names = array(
        'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover Single Size',
        'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover Double Size',
        'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover King Size',
        'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover King Single',
        'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover Queen Size'
    );
    // Parent  for 2. 'Himalayan Salt Lamp Rock Crystal Natural Light Dimmer Cord Globes'
    $names = array(
        '3-5 kg Himalayan Salt Lamp Rock Crystal Natural Light Dimmer Switch Cord Globes',
        '5-7 kg Himalayan Salt Lamp Rock Crystal Natural Light Dimmer Switch Cord Globes'
    );
    // Parent  for 2. 'Air Track Inflatable Mat Airtrack Tumbling Electric Air Pump Gymnastics'
    $names = [  
        '4x1M Inflatable Air Track Mat Tumbling Pump Floor Home Gymnastics Gym in Red',
        '5x1M Air Track Inflatable Mat Airtrack Tumbling Electric Air Pump Gymnastics',
        '5x1M Air Track Inflatable Mat Airtrack Tumbling Electric Air Pump Gymnastics',
        '6x1M Air Track Inflatable Mat Airtrack Tumbling Electric Air Pump Gymnastics',
        '6x1M Air Track Inflatable Mat Airtrack Tumbling Electric Air Pump Gymnastics'
    ];
    
    $skus = array(
        'EE1501',
        'EE1501-D',
        'EE1501-K',
        'EE1501-KS',
        'EE1501-Q'
    );

    $x = 'DreamZ Fitted Waterproof Mattress Protector with Bamboo Fibre Cover Single Size';


    $titles = array();
    $collected_titles = array();
    $sliced_word = array();    
    foreach($names as $n){
        $namesexp = explode(' ',$n);
        array_push($sliced_word, $namesexp);
        array_push($titles,$namesexp);
        foreach($namesexp as $nexp){
            array_push($collected_titles,$nexp);
        }
    }
    
    $count_arry = array_count_values($collected_titles);
    echo "Collected Array <br>";
    ar_to_pre($names);
    echo "<hr bgcolor='red'>";
    echo "Sliced Each Word <br>";
    ar_to_pre($sliced_word);
    echo "<hr>";
    echo "Collected Titles <br>";
    ar_to_pre($collected_titles);
    echo "<hr>";
    echo "Got the frequency each word <br>";
    ar_to_pre($count_arry);
    echo "<hr>";

    foreach($count_arry as $k =>  $ca){
        if($ca >= (count($names)))
            $parent_title .= $k . " ";
    }
    echo "Collected All the Frequency then Compared to array_count <br>";

    echo "<b>" . $parent_title . "</b>";
    echo "<hr>";
    
    echo "compare each word of the title then compare it to each of the sliced word then store its order<br>";
    //echo $parent_title;
    foreach($sliced_word as $sw){
        echo "1";ar_to_pre($sw);
    }
    echo "<hr>";
?>