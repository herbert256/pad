<?php
$exampleResult = padSchema('relation',['people'=>[['id'=>1]],'orders'=>[['person'=>1]]],['primary'=>[['table'=>'people','fields'=>['id']]],'foreign'=>[['table'=>'orders','fields'=>['person'],'references'=>['table'=>'people','fields'=>['id']]]]]);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
