<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/calendars/_lib/capacity.php';$w=[['start'=>'2026-10-25T01:00:00+02:00','end'=>'2026-10-25T04:00:00+01:00','capacity'=>2]];$b=[['start'=>'2026-10-25T02:00:00+02:00','end'=>'2026-10-25T03:00:00+01:00','load'=>3]];$s=padCalendarCapacity($w,$b,'Europe/Amsterdam');check(count($s)===3);check($s[1]['over']===1.0);check(str_ends_with($s[1]['start'],'+02:00')&&str_ends_with($s[1]['end'],'+01:00'));try{padCalendarCapacity([$w[0],$w[0]],[]);check(false);}catch(InvalidArgumentException $e){}
