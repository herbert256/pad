<?php
$exampleResult = padNumeric('root',['function'=>fn($x)=>$x*$x-2,'lower'=>0,'upper'=>2],['tolerance'=>1e-10]);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT);
?>
