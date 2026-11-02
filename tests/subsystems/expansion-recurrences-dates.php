<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/recurrence.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$r=['mode'=>'date','dates'=>['2026-01-03','2026-01-01','2026-01-03','2026-01-02'],'exclude'=>['2026-01-02']];check(padOccurrences('dates',$r,'2026-01-01','2026-01-04')===['2026-01-01','2026-01-03']);$r=['mode'=>'instant','dates'=>['2026-01-01T01:00:00+01:00','2026-01-01T00:00:00Z','2026-01-01T02:00:00Z']];check(padOccurrences('dates',$r,'2026-01-01','2026-01-02')===['2026-01-01T00:00:00+00:00','2026-01-01T02:00:00+00:00']);rejects(fn()=>padOccurrences('dates',['mode'=>'date','dates'=>['2026-01-01T12:00']],'2026-01-01','2026-01-02'));check(padOccurrences('dates',['mode'=>'date','dates'=>[]],'2026-01-01','2026-01-02')===[]);
echo "recurrences:dates fixtures passed\n";
