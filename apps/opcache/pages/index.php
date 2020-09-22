<?php

define('THOUSAND_SEPARATOR',true);

if (!extension_loaded('Zend OPcache')) {
    echo '<div style="background-color: #F2DEDE; color: #B94A48; padding: 1em;">You do not have the Zend OPcache extension loaded, sample data is being shown instead.</div>';
    require 'data-sample.php';
}

$dataModel = new OpCacheDataModel();

?>