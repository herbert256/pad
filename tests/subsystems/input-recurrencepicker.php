<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/inputs/_lib/recurrencepicker.php';$r=padInputRecurrence(['kind'=>'daily','start'=>'2026-03-28T02:30:00','timezone'=>'Europe/Amsterdam','interval'=>1,'count'=>3,'exclude'=>'']);check(count($r['occurrences'])===3);check(str_contains($r['occurrences'][1],'2026-03-30'));try{padInputRecurrence(['kind'=>'daily','start'=>'2026-01-01','byday'=>'MO']);check(false);}catch(InvalidArgumentException $e){}
