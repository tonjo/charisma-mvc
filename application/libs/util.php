<?php

Class Util {

    public static function myprint($what,$die = false) {
        echo '<pre>';
        print_r($what);
        echo '</pre>';
        if (isset($die) && $die)
            die;
    }

    /**
     * FROM PHP ONE FILE LOGIN
     * Performs a check for minimum requirements to run this application.
     * Does not run the further application when PHP version is lower than 5.3.7
     * Does include the PHP password compatibility library when PHP version lower than 5.5.0
     * (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
     * @return bool Success status of minimum requirements check, default is false
     */

    public static function performMinimumRequirementsCheck()
    {
        if (version_compare(PHP_VERSION, '5.3.7', '<')) {
            return "Questa applicazione richiede una versione PHP >= 5.3.7 !";
        } elseif (version_compare(PHP_VERSION, '5.5.0', '<')) {
            require_once("application/libs/password_compatibility_library.php");
            return true;
        } elseif (version_compare(PHP_VERSION, '5.5.0', '>=')) {
            return true;
        }
        // default return
        return false;
    }

    public static function random_str($length) {
        return substr(md5(rand(0, 1000000)), 0, $length);
    }

    // Courtesy of Sam on stackoverflow
    public static function cidr_match($ip, $cidr)
    {
        list($subnet, $mask) = explode('/', $cidr);

        if ((ip2long($ip) & ~((1 << (32 - $mask)) - 1) ) == ip2long($subnet))
        {
            return true;
        }

        return false;
    }

    public static function isrange($net) {
        return (strpos($net,'-'));
    }

    public static function IPrange_match($testip,$testnet)
    {
        $net = explode('.', $testnet);
        if (strpos($net[3],'-')) {
            $ip = explode('.',$testip);
            if ( ($ip[0] != $net[0]) || ($ip[1] != $net[1]) || ($ip[2] != $net[2]))
                return false;
            list($from,$to) = explode('-',$net[3]);
            $from = (int) $from;
            $to = (int) $to;
            $ipcheck = (int) $ip[3];
            return ( ($ipcheck >= $from) && ($ipcheck <= $to) );
        }
        else
            return ($testip == $testnet);
    }

    // NOT USED YET
    // function validate_date($date, $format = 'Y-m-d H:i:s')
    // {
    //     $d = DateTime::createFromFormat($format, $date);
    //     return $d && $d->format($format) == $date;
    // }
}

?>