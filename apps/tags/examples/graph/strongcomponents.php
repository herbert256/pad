<?php
$exampleResult = padGraph('strongcomponents',['a','b','c'],[['a','b'],['b','a'],['b','c']]);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
