<?php
/**
 * GGAutoloader
 */
class GGAutoloader {

    public static function init() {
       return spl_autoload_register(array('GGAutoloader','LoadLibrary'), false, true);
    }

    public static function LoadLibrary($ClassName) {
		if (class_exists($ClassName,FALSE)) {
            return FALSE;
        }
        $ClassFilePath = GOGLOBALAPI_ROOT . '/lib/' . $ClassName . '.php';
        if ((file_exists($ClassFilePath) === FALSE) || (is_readable($ClassFilePath) === FALSE)) {
            return FALSE;
        }
        require($ClassFilePath);
    }
}

GGAutoloader::init();
