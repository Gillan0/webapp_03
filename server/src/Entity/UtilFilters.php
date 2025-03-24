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
    public static function isValidDate(\DateTimeInterface $deadline): bool
{
    if (!UtilFilters::isValid($deadline)) {
        return false;
    }
    $currentDate = new DateTime(); 
    
    if ($deadline <= $currentDate) { 
        return false;
    }
    
    
    return true;
}
    
    
    /**
     * Checks date format 
     * 
     * @param mixed $date
     * @param mixed $format
     * @return bool
     */
    public static function isValid($date, $format = 'Y-m-d H:i:s'){
        $formattedDate = $date->format($format);
        $reconstructedDateTime = DateTime::createFromFormat($format, datetime: $formattedDate);
        
        return $reconstructedDateTime && $reconstructedDateTime->format($format) === $formattedDate;

    }

}