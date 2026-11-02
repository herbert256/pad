<?php
include_once PAD.'subs/times/_lib/duration.php';
$padDurationUnits=padTagParm('units',4);
try {if(!is_scalar($padDurationUnits)||!ctype_digit((string)$padDurationUnits))throw new InvalidArgumentException('duration units must be 1..4');return padTimeDuration($padParm,(int)$padDurationUnits,padTagParm('rounding','nearest'));}
catch(InvalidArgumentException $e){if($padCheckSyntax)padError($e->getMessage());return FALSE;}
