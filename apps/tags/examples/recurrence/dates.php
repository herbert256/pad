<?php
$exampleResult = padOccurrences('dates',['mode'=>'date','dates'=>['2026-11-10','2026-11-02','2026-11-10']],'2026-11-01','2026-12-01');
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
