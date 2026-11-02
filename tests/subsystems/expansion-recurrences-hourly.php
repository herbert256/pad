<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/recurrence.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$r=['start'=>'2026-10-25T00:30:00+02:00','timezone'=>'Europe/Amsterdam'];$elapsed=padOccurrences('hourly',$r+['step'=>'elapsed'],'2026-10-25','2026-10-25T05:00');$wall=padOccurrences('hourly',$r+['step'=>'wall'],'2026-10-25','2026-10-25T05:00');check(count($elapsed)===6&&count($wall)===5);check($elapsed[2]==='2026-10-25T02:30:00+02:00'&&$elapsed[3]==='2026-10-25T02:30:00+01:00');$r=['start'=>'2026-03-29T00:30:00+01:00','timezone'=>'Europe/Amsterdam','step'=>'wall'];$a=padOccurrences('hourly',$r,'2026-03-29','2026-03-29T05:00');check(count($a)===4&&!str_contains(implode(',',$a),'T02:30'));$a=padOccurrences('hourly',$r+['count'=>3,'exclude'=>['2026-03-29T01:30:00+01:00']],'2026-03-29','2026-03-30');check(count($a)===2);rejects(fn()=>padOccurrences('hourly',['start'=>'2026-01-01'],'2026-01-01','2026-01-02'));
check(count(padOccurrences('hourly',['start'=>'2026-01-01T00:00','count'=>1,'maxSteps'=>1],'2026-01-01','2026-01-02'))===1);
echo "recurrences:hourly fixtures passed\n";
