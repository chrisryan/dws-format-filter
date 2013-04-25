<?php
require_once 'PHP/Beautifier.php';
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__DIR__) . '/filters');

function filterAutoload($sClassQuasiPath)
{
    if (strpos($sClassQuasiPath, 'PHP_Beautifier_Filter_') === false) {
        return false;
    }

    return (bool)include str_replace('PHP_Beautifier_Filter_', '', $sClassQuasiPath) . '.filter.php';
}
spl_autoload_register('filterAutoload');
