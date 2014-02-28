<?php namespace Plugpress;


function is_valid_time($time){
    return preg_match("/(0?\d|1[0-2]):(0\d|[0-5]\d) (AM|PM)/i", trim($time), $matches);
}

function hhmmxm_to_hhmmss($time){
    $time = parse_time($time);  
    return sprintf("%02s", $time['hour']) .":" .sprintf("%02s", $time['minute']) .":" .sprintf("%02s", $time['second']);
}


function parse_time($time){
    if(is_valid_time($time)){
        $time = explode(" ", trim($time));
        $xm   = $time[1];
        $time = $time[0];
    }
    
    $time = date_parse($time);
    if(strtolower(trim($xm)) === 'pm') {
        $time['hour'] += 12;
    } 
    
    return $time;
}


function hhmmxm_to_seconds($time){

    $time = parse_time($time);
    
    $time_in_seconds = ($time['hour'] * 3600) + ($time['minute'] * 60);
    
    exit(var_dump($time_in_seconds));
    return $time_in_seconds;
}


function strip_non_numeric($str){
    return preg_replace('/\D/', '', $str);
}

?>
