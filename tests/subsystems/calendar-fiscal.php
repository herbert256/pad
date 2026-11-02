<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/calendars/_lib/fiscal.php';$a=[['label'=>'Q1','start'=>'2026-04-01','end'=>'2026-07-01'],['label'=>'Q2','start'=>'2026-07-01','end'=>'2026-10-01']];check(count(padCalendarFiscal($a,'2026-04-01','2026-10-01'))===2);$a[1]['start']='2026-07-02';try{padCalendarFiscal($a);check(false);}catch(InvalidArgumentException $e){}
