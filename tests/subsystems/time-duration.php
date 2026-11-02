<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function padChartAttr($s){return htmlspecialchars($s,ENT_QUOTES);}require PAD.'subs/times/_lib/countdown.php';require PAD.'subs/times/_lib/duration.php';check(str_contains(padTimeDuration(0),'PT0S'));check(str_contains(padTimeDuration(-3661),'−1 hour, 1 minute, 1 second'));check(str_contains(padTimeDuration(1.5),'PT2S'));try{padTimeDuration(INF);check(false);}catch(InvalidArgumentException $e){}
