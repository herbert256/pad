<?php
$exampleResult = padStats('bootstrap',[1,2,3,4,5],['seed'=>42,'resamples'=>500,'interval'=>'percentile']);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
