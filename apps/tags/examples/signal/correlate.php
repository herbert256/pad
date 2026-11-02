<?php
$exampleResult = padSignal('correlate',['a'=>[1,0,0],'b'=>[0,0,1]],['sampleRate'=>10,'normalization'=>'none']);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT);
?>
