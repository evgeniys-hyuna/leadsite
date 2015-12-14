<?php

/*
 * <evgeniy.siderka@hyuna.bb>
 */

/**
 * Description of Time
 *
 * @author jomedia_64
 */
class Time {
    const SECONDS_IN_MINUTE = 60;
    const SECONDS_IN_HOUR = 3600;
    const SECONDS_IN_DAY = 86400;
    const SECONDS_IN_WEEK = 604800;
    const SECONDS_IN_MONTH = 2592000;
    const SECONDS_IN_YEAR = 31557600;
    
    const FORMAT_STANDART = 'Y-m-d H:i:s';
    const FORMAT_PRETTY = 'd M Y H:i';
    const FORMAT_FULL = 'j F, Y H:i';
    const FORMAT_DATE = 'Y-m-d';
    const FORMAT_DATE_PRETTY = 'd M Y';
    const FORMAT_TIME = 'H:i:s';
    
    public static function toPretty($dateString) {
        return date(self::FORMAT_PRETTY, strtotime($dateString));
    }
    
    public static function toFormat($format, $dateString) {
        return date($format, strtotime($dateString));
    }
    
    public static function getDaysOfTheWeek($fullNames = false, $indexed = false) {
        $dayNames = $fullNames ? array(
            'Sunday', 
            'Monday', 
            'Tuesday', 
            'Wednesday', 
            'Thursday', 
            'Friday', 
            'Saturday',
        ) : array(
            'Sun', 
            'Mon', 
            'Tue', 
            'Wed', 
            'Thu', 
            'Fri', 
            'Sat',
        );
        
        $result = array();
        
        for ($i = 0; $i < count($dayNames); $i++) {
            array_push($result, array(
                'number' => $i + 1,
                'name' => $dayNames[$i],
            ));
        }
        
        return $indexed ? $dayNames : $result;
    }
    
    public static function getDatesOfTheMonth($lastDate = 31) {
        return range(1, $lastDate);
    }
    
    public static function getMonthsOfTheYear($fullNames = false, $indexed = false) {
        $monthNames = $fullNames ? array(
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ) : array(
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Feb',
        );
        
        $result = array();
        
        for ($i = 0; $i < count($monthNames); $i++) {
            array_push($result, array(
                'number' => $i + 1,
                'name' => $monthNames[$i],
            ));
        }
        
        return $indexed ? $monthNames : $result;
    }
}
