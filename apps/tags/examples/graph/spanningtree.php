<?php
$exampleResult = padGraph('spanningtree',['a','b','c'],[['a','b',5],['a','c',2],['b','c',1]]);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
