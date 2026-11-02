<?php
$o=[];foreach(['text'=>'','mark'=>'','alt'=>NULL,'width'=>600,'height'=>0,'position'=>'bottom-right','opacity'=>.5,'scale'=>.25] as $key=>$default)$o[$key]=padTagParm($key,$default);
try{return padImageWatermark($padParm,$o);}catch(InvalidArgumentException $e){if($padCheckSyntax)padError($e->getMessage());return '';}
