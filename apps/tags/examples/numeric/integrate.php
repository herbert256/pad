<?php
$exampleResult = padNumeric('integrate',[[0,0],[1,2],[2,4]],['method'=>'trapezoid']);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT);
?>
