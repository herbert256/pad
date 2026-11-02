<?php
$exampleResult = padTextAnalysis('keywords',['first'=>'PAD templates produce pages','second'=>'PAD pages contain data'],['normalization'=>'none','method'=>'tfidf']);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
