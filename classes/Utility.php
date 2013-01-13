<?php

class Utility{
    public static function TicksToTime($ticks){
        return floor(($ticks - 621355968000000000) / 10000000);
    }
    
    public static function TimeToTicks($time){
        return number_format(($time * 10000000) + 621355968000000000 , 0, '.', '');         
    }
    
    public static function TimeToDotNetJSONDate($time){
        if($time != null){
            return '\\/Date('.$time.'000)\\/';
        }else{
            return null;
        }
    }
    
    public static function DotNetJSONDateToTime($jsonDate){
        if($jsonDate != null){
            preg_match( '/([\d]{10})/', $jsonDate, $matches );
            return $matches[0];            
        }else{
            return null;
        }
    }
    
    public static function TimeToEnUSFormat($time){
        //month/day/yearhour:minute:second AM/PM.
        return date('n/j/Y g:i:s A', $time);
    }
    
    public static function GetFileUrl($locatorPath, $filename){
        return substr_replace($locatorPath, '/' . $filename, strpos($locatorPath, '?st='), 0);
    }
}

?>