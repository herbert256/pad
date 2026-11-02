<?php
$box=padFakeOpt('box',[-90,-180,90,180]);
$method=padFakeOpt('method','surface');
if(!is_array($box)||count($box)!==4||array_filter($box,fn($v)=>!is_numeric($v)||!is_finite((float)$v))||!in_array($method,['surface','degrees'],TRUE)) {
 if($GLOBALS['padCheckSyntax']??TRUE)padError('fake:coordinate expects box=[south,west,north,east] and method=surface or degrees');return NULL;
}
[$s,$w,$n,$e]=array_map('floatval',array_values($box));
if($s< -90||$n>90||$w< -180||$e>180||$s>$n||$w>$e){if($GLOBALS['padCheckSyntax']??TRUE)padError('fake:coordinate box must be ordered within latitude/longitude bounds');return NULL;}
$u=padFakeNumber(0,1000000000)/1000000000;
$lat=$method==='surface'?rad2deg(asin(sin(deg2rad($s))+$u*(sin(deg2rad($n))-sin(deg2rad($s))))):$s+$u*($n-$s);
return ['latitude'=>max($s,min($n,round($lat,8))),'longitude'=>max($w,min($e,round($w+padFakeNumber(0,1000000000)/1000000000*($e-$w),8)))];
