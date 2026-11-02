<?php
$exampleResult = padOccurrences('hourly',['start'=>'2026-10-25T00:30:00+02:00','timezone'=>'Europe/Amsterdam','step'=>'elapsed'],'2026-10-25','2026-10-25T05:00');
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
