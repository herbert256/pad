<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/printables/_lib/timesheet.php';$p=padPrintableTimesheetPages(array_fill(0,100,'Ada | Project A | 2026-11-02 | 1:30'),[210,297],'clock',2);check(count($p)>1);check(array_sum(array_map('count',$p))===102);try{padPrintableTimesheetPages(['Ada | P | 2026-02-30 | 1:90'],[210,297],'clock');check(false);}catch(InvalidArgumentException $e){}
