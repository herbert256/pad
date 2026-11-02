<?php
$exampleResult = padSolve('knapsack',['items'=>[['id'=>'A','weight'=>3,'value'=>5],['id'=>'B','weight'=>4,'value'=>8]],'capacity'=>5]);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
