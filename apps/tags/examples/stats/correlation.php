<?php
$exampleResult = padStats('correlation',[['x'=>1,'y'=>2],['x'=>2,'y'=>5],['x'=>3,'y'=>6]],['method'=>'pearson']);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
