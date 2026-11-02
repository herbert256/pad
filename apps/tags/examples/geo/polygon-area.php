<?php
$exampleResult = padGeo('polygon-area',['type'=>'Polygon','coordinates'=>[[[4,51],[5,51],[5,52],[4,52],[4,51]]]],NULL,['unit'=>'km2']);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
