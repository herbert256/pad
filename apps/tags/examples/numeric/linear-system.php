<?php
$exampleResult = padNumeric('linear-system',['matrix'=>[[2,1],[1,3]],'rhs'=>[5,7]]);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT);
?>
