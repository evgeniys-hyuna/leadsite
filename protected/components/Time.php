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
    const FORMAT_TIME = 'H:i:s';
    
    public static function toPretty($dateString) {
        return date(self::FORMAT_PRETTY, strtotime($dateString));
    }
}
