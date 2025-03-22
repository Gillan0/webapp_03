<?php

namespace App\Misc;

// Check the validity of the date (format and if the deadline is in the past)
function isValidDate(\DateTimeInterface $deadline){
    if (isValid($deadline)){
        $date = new DateTime();
        if ($deadline>$date->format('Y-m-d H:i:s')){
            return true;
        }
        return false;            
    }
    return false;
}


// Check the format of a date
function isValid($date, $format = 'Y-m-d H:i:s'){
    $dt = DateTime::createFromFormat($format, $date);
    return $dt && $dt->format($format) === $date;
    }


?>