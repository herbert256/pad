<?php
$exampleResult = padSample('gamma',8,['shape'=>2,'scale'=>3,'seed'=>42]);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
