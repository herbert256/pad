<?php
$exampleResult = padTextAnalysis('concordance','PAD pages use PAD templates.',['normalization'=>'none','query'=>'pad','context'=>12]);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
