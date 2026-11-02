<?php
$exampleResult = padSample('hypergeometric',8,['population'=>20,'successes'=>7,'draws'=>5,'seed'=>42]);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
