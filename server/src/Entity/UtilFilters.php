<?php
namespace App\Entity;
use \DateTime;
use \DateTimeInterface;

/**
 * Util class containing methods to check the validity of inputs.
 * 
 * @author Antonino Gillard <antonino.gillard@imt-atlantique.net>
 * @author Lucien Duhamel <lucien.duhamel@imt-atlantique.net>
 */
class UtilFilters 
{
    /**
     * Checks if date is set in the future and proper format
     * @param \DateTimeInterface $deadline
     * @return bool
     */
    public static function isValidDate(\DateTimeInterface $deadline){
        if (UtilFilters::isValid($deadline)){
            $date = new DateTime();
            if ($deadline>$date->format('Y-m-d H:i:s')){
                return true;
            }
            return false;            
        }
        return false;
    }
    
    
    /**
     * Checks date format 
     * 
     * @param mixed $date
     * @param mixed $format
     * @return bool
     */
    public static function isValid($date, $format = 'Y-m-d H:i:s'){
        $dt = DateTime::createFromFormat($format, $date->format('Y-m-d H:i:s'));
        return $dt && $dt->format($format) === $date;
    }

}