<?php
$exampleResult = padStats('weighted',[['value'=>1,'weight'=>2],['value'=>4,'weight'=>1]],['semantics'=>'frequency']);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
