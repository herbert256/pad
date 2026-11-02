<?php
$exampleResult = padFormula('=CHOOSECOLS(A1:B2,-1,1)',['A1'=>1,'B1'=>2,'A2'=>3,'B2'=>4]);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
