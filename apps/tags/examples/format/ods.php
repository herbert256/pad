<?php
$exampleResult = padFormatRead('ods',padFormatWrite('ods',[['Name'=>'Ada','Score'=>42],['Name'=>'Bo','Score'=>7]],['sheet'=>'Scores']),['sheet'=>'Scores']);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
