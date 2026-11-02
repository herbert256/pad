<?php
$exampleResult = padSample('geometric',8,['p'=>.25,'seed'=>42]);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
