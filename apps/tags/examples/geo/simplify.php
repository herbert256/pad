<?php
$exampleResult = padGeo('simplify',['type'=>'LineString','coordinates'=>[[4,52],[4.01,52.001],[4.02,52]]],NULL,['tolerance'=>200]);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
