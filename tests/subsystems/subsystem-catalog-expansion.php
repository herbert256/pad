<?php
define('PAD',dirname(__DIR__,2).'/pad/');define('APPS',dirname(__DIR__,2).'/apps/');
function padDirList($p){return scandir($p);}function padError($m){throw new InvalidArgumentException($m);}function padFileGet($p){return file_get_contents($p);}
require PAD.'lib/subsystem.php';
foreach(['statistics','graphs','schemas','textanalysis','constraints','units','geospatial','fake','distributions','recurrence','intervals','colors','timeseries','geometry','numerics','signals'] as $library)require PAD."lib/$library.php";
require APPS.'_common/_lib/subsystemCatalog.php';
set_error_handler(function($n,$message){throw new RuntimeException($message);});$checks=0;
foreach(['stats','graph','schema','textanalysis','solve','unit','geo','distribution','recurrence','interval','color','timeseries','geometry','numeric','signal'] as $family){
 if(!isset(appSubsystemFamilies()[$family]))throw new RuntimeException('Undiscoverable family '.$family);
 foreach(padKinds(padSubsystemKinds[$family]) as $kind){$example=appSubsystemExample($family,$kind);if(!$example['source']||$example['language']!=='php')throw new RuntimeException('Missing helper example');eval($example['source']);$checks++;}
}
foreach(padSubsystemKinds as $directory)foreach(padKinds($directory) as $name)if(!is_string($name))throw new RuntimeException('Registry names must be strings');
echo "$checks catalog helper examples executed without warnings\n";
