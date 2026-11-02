<?php
$exampleResult = padSolve('coloring',['nodes'=>['a','b','c'],'edges'=>[['a','b'],['b','c']],'colors'=>2]);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
