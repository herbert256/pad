<?php
$exampleResult = padSignal('convolve',['signal'=>[1,2,3],'kernel'=>[.5,.5]],['mode'=>'full']);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT);
?>
