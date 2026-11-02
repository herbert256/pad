<?php
$exampleResult = padSignal('spectrum',[0,1,0,-1,0,1,0,-1],['sampleRate'=>8,'window'=>'rectangular','scaling'=>'amplitude']);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT);
?>
