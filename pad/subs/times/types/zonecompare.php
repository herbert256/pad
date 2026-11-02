<?php
include_once PAD.'subs/times/_lib/zonecompare.php';
$padZoneInstant=padDateParse($padParm);
try{return padTimeZoneCompare($padZoneInstant,padTagParm('zones',['UTC','Europe/Amsterdam']),padTagParm('format','Y-m-d H:i'));}
catch(InvalidArgumentException $e){if($padCheckSyntax)padError($e->getMessage());return FALSE;}
