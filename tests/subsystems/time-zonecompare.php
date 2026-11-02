<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function padChartAttr($s){return htmlspecialchars($s,ENT_QUOTES);}require PAD.'subs/times/_lib/zonecompare.php';$d=new DateTimeImmutable('2026-07-01T12:00:00Z');$a=padTimeZoneCompare($d,['UTC','Europe/Amsterdam']);check(str_contains($a,'14:00 +02:00'));check($d->format('H:i')==='12:00');try{padTimeZoneCompare($d,['Bad/Zone']);check(false);}catch(InvalidArgumentException $e){}
